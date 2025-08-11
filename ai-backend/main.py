from fastapi import FastAPI, UploadFile, File
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from fastapi import Request
from pydantic import BaseModel

from langchain.chains.question_answering import load_qa_chain
from langchain.text_splitter import CharacterTextSplitter
from langchain_core.documents import Document

from langchain_community.vectorstores import FAISS
from langchain_community.document_loaders import TextLoader
from langchain_ollama import OllamaEmbeddings, OllamaLLM

from dotenv import load_dotenv
import os
import shutil

# Load env vars (if any)
load_dotenv()

# === Konfigurasi direktori upload
UPLOAD_DIR = "../bahan-chatbot/txt"
os.makedirs(UPLOAD_DIR, exist_ok=True)

app = FastAPI()

# === Aktifkan CORS agar bisa dipanggil dari frontend manapun
# Middleware CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Ubah sesuai kebutuhan
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# === Variabel global
vectordb = None
qa_chain = None
retriever = None
loaded_files = []

# === Fungsi untuk memuat dan vektorisasi dokumen TXT
def load_documents():
    global vectordb, qa_chain, loaded_files, retriever

    folder_path = UPLOAD_DIR
    documents = []

    for filename in os.listdir(folder_path):
        if filename.endswith(".txt"):
            filepath = os.path.join(folder_path, filename)
            loader = TextLoader(filepath, encoding="utf-8")
            file_docs = loader.load()
            documents.extend(file_docs)
            if filename not in loaded_files:
                loaded_files.append(filename)

    print(f"✅ Jumlah chunks: {len(documents)}")

    if not documents:
        print("⚠️ Tidak ada dokumen yang dimuat.")
        return

    # Embedding & Vectorstore
    embeddings = OllamaEmbeddings(model="nomic-embed-text")
    vectordb = FAISS.from_documents(documents, embeddings)

    # Tambah retriever
    retriever = vectordb.as_retriever()

    # Language Model & QA Chain
    llm = OllamaLLM(model="llama3")
    qa_chain = load_qa_chain(llm, chain_type="stuff")

# === Endpoint upload file
@app.post("/upload")
async def upload_file(file: UploadFile = File(...)):
    try:
        filename = file.filename
        file_ext = filename.split(".")[-1].lower()

        if file_ext not in ["txt", "pdf"]:
            return JSONResponse(content={"error": "Hanya file .txt atau .pdf yang diperbolehkan"}, status_code=400)

        save_path = os.path.join(UPLOAD_DIR, filename)
        with open(save_path, "wb") as buffer:
            shutil.copyfileobj(file.file, buffer)

        if file_ext == "txt":
            load_documents()

        return {"message": f"{filename} berhasil diupload"}
    except Exception as e:
        return JSONResponse(content={"error": str(e)}, status_code=500)

# === Endpoint tanya jawab
# @app.get("/chat")
# async def ask(q: str):
#     if vectordb is None or qa_chain is None:
#         return JSONResponse(content={"error": "Dokumen belum dimuat atau model belum siap"}, status_code=400)
#     try:
#         docs = vectordb.similarity_search(q)
#         answer = qa_chain.run(input_documents=docs, question=q)
#         return {"question": q, "answer": answer}
#     except Exception as e:
#         return JSONResponse(content={"error": str(e)}, status_code=500)

# Data model input chat
class ChatRequest(BaseModel):
    question: str
    
# Endpoint POST /chat
@app.post("/chat")
async def chat(req: ChatRequest):
    question = req.question
    print("Pertanyaan masuk:", question)
    
    docs = retriever.get_relevant_documents(question)
    answer = qa_chain.run(input_documents=docs, question=question)

    return JSONResponse(content={"answer": answer})

# === Endpoint untuk melihat daftar dokumen
@app.get("/files")
def get_files():
    return {"loaded_files": loaded_files}

# === Event saat startup FastAPI
@app.on_event("startup")
def startup_event():
    load_documents()

@app.get("/")
def read_root():
    return {"message": "API is running"}

@app.get("/chat")
async def chat(request: Request):
    return {"message": "Gunakan POST dengan body JSON untuk mengirim pertanyaan"}

#parse_content
#parse_item
#parse
#fetch_page
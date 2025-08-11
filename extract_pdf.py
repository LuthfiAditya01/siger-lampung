# extract_pdf.py
import sys
from PyPDF2 import PdfReader

if len(sys.argv) != 3:
    print("Usage: python extract_pdf.py input.pdf output.txt")
    sys.exit(1)

pdf_path = sys.argv[1]
txt_path = sys.argv[2]

try:
    reader = PdfReader(pdf_path)
    all_text = ""
    for page in reader.pages:
        all_text += page.extract_text() + "\n"

    with open(txt_path, "w", encoding="utf-8") as f:
        f.write(all_text.strip())

    print("Ekstraksi berhasil!")
    sys.exit(0)
except Exception as e:
    print(f"Gagal: {e}")
    sys.exit(1)

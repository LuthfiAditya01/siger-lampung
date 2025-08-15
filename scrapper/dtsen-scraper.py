import requests
from bs4 import BeautifulSoup
import pandas as pd
from datetime import datetime
import time
import os
import json
import re
import mysql.connector

def get_search_results_antaranews(keyword, max_pages, retries, timeout):
    base_url = f"https://lampung.antaranews.com/search?q={keyword}&page="
    links = []
    page = 1
    
    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []

    for page in range(1, max_pages + 1):
        url = base_url + str(page)
        for attempt in range(retries):
            try:
                response = requests.get(url, timeout)
                response.raise_for_status()
                break
            except (requests.ConnectionError, requests.Timeout) as e:
                print(f"Attempt {attempt+1} failed: {e}")
                if attempt < retries - 1:
                    time.sleep(2 ** attempt)
                else:
                    print(f"Failed to retrieve page {page}. Skipping.")
                    return links
            except requests.RequestException as e:
                print(f"Request failed: {e}")
                return links

        soup = BeautifulSoup(response.content, 'html.parser')
        col_md8 = soup.find('div', class_='col-md-8')

        if col_md8:
            h3_tags = col_md8.find_all('h3', limit=10)
            for h3 in h3_tags:
                link = h3.find('a', href=True)
                if link and 'berita' in link['href']:
                    links.append(link['href'])
                    tautan = link['href']
                    judul = link['title']
            p_tags = col_md8.find_all('p', limit=10)
            for p in p_tags:
                span = p.find('span')
                if span:
                    tanggal_text = span.get_text(strip=True)
                    if "jam" in tanggal_text.lower():
                        # Kalau ada kata "jam", langsung set kemarin
                        tanggal_format = (datetime.today() - timedelta(days=1)).strftime("%Y-%m-%d")
                    else:
                        tanggal_text_clean = re.sub(r'Wib.*', '', tanggal_text, flags=re.IGNORECASE).strip()
                        dt = datetime.strptime(tanggal_text_clean, "%d %B %Y %H:%M")
                        tanggal_format = dt.strftime("%Y-%m-%d")
                    
        nama_media = "Antara News"
        # Append ke list
        tanggal_list.append(tanggal_format)
        nama_media_list.append(nama_media)
        judul_list.append(judul)
        link_list.append(tautan)

        # Memeriksa apakah ada halaman berikutnya untuk diakses lagi
        pagination = soup.find('ul', class_='pagination pagination-sm')
        if pagination:
            next_page = None
            for a_tag in pagination.find_all('a'):
                if a_tag.get('aria-label') == 'Next':
                    next_page = a_tag.get('href')
                    break
            if next_page:
                page=page+1
            else:
                break
        else:
            break
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_viva(keyword, max_pages, retries, timeout):
    base_url = "https://lampung.viva.co.id/search?q={keyword}"
    page = 1
    links = []

    bulan_map = {
                "Januari": "01", "Februari": "02", "Maret": "03", "April": "04",
                "Mei": "05", "Juni": "06", "Juli": "07", "Agustus": "08",
                "September": "09", "Oktober": "10", "November": "11", "Desember": "12"
    }
    
    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []

    url = base_url.format(keyword=keyword, page=page)
    
    for attempt in range(retries):
        try:
            response = requests.get(url, timeout)
            response.raise_for_status()
            break
        except (requests.ConnectionError, requests.Timeout) as e:
            # print(f"Attempt {attempt + 1} failed: {e}")
            if attempt < retries - 1:
                time.sleep(2 ** attempt)
            else:
                # print(f"Failed to retrieve page {page}. Skipping.")
                return links
        except requests.RequestException as e:
            # print(f"Request failed: {e}")
            return links

    soup = BeautifulSoup(response.text, 'html.parser')

    # Cari container besar
    container = soup.find("div", class_="column-big-container")

    for article in container.find_all("div", class_="article-list-row"):
        info = article.find("div", class_="article-list-info content_center")

        # Ambil href di <a>
        a_tag = info.find("a")
        href = a_tag.get("href") if a_tag else None

        # Ambil judul di <h2>
        h2_tag = info.find("h2")
        judul = h2_tag.get_text(strip=True) if h2_tag else None

        # Ambil tanggal
        date_div = info.find("div", class_="article-list-date content_center")
        tanggal_text = date_div.get_text(strip=True) if date_div else None
        if tanggal_text:
            tanggal_only = tanggal_text.split("|")[0].strip()
            tgl_parts = tanggal_only.split()
            if len(tgl_parts) == 3:
                day = tgl_parts[0]
                month = bulan_map.get(tgl_parts[1], "01")
                year = tgl_parts[2]
                tanggal_format = f"{year}-{month}-{day}"
            else:
                tanggal_format = None
        else:
            tanggal_format = None

        nama_media = "Viva Lampung"
        # Append ke list
        tanggal_list.append(tanggal_format)
        nama_media_list.append(nama_media)
        judul_list.append(judul)
        link_list.append(href)
            
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_lampungpost(keyword, max_pages, timeout):
    base_url = f"https://lampost.co/page/{{}}/?s={keyword}"
    
    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []
    
    for page in range(1, max_pages + 1):
        url = base_url.format(page)
        response = requests.get(url, timeout)
        soup = BeautifulSoup(response.content, 'html.parser')

        articles = soup.find_all("article", class_="jeg_post")

        for art in articles:
            # Ambil link & judul
            title_tag = art.find("h3", class_="jeg_post_title").find("a")
            judul = title_tag.get_text(strip=True)
            link = title_tag["href"]

            # Ambil tanggal
            date_tag = art.find("div", class_="jeg_meta_date").find("a")
            tanggal_raw = date_tag.get_text(strip=True) 
            tanggal_format = datetime.strptime(tanggal_raw, "%d/%m/%Y").strftime("%Y-%m-%d")

            nama_media = "Lampung Post"
            # Append ke list
            tanggal_list.append(tanggal_format)
            nama_media_list.append(nama_media)
            judul_list.append(judul)
            link_list.append(link)
        
        pagination_div = soup.find("div", class_="jeg_navigation")
        if pagination_div:
            page += 1
        else:
            break
    
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_sinarlampung(keyword, max_pages, timeout):
    base_url = f"https://sinarlampung.co/search/?q={keyword}&page="
    page = 1

    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []

    while page <= max_pages:
        url = base_url + str(page)
        headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36"
        }
        response = requests.get(url, headers=headers, timeout=timeout)
        if response.status_code == 200:
            soup = BeautifulSoup(response.content, 'html.parser')
            articles = soup.find_all("article", class_="flex flex-col md:flex-row gap-4 bg-[#1e293b] rounded-lg overflow-hidden hover:bg-[#1e293b]/80 transition-colors duration-300 shadow-md group")

            for art in articles:
                # href
                link_tag = art.find("a", class_="block h-full")
                href = "https://sinarlampung.co" + link_tag["href"] if link_tag else None

                # alt
                img_tag = art.find("img")
                alt = img_tag["alt"] if img_tag else None

                # datetime -> YYYY-MM-DD
                time_tag = art.find("time")
                date_str = None
                if time_tag and time_tag.has_attr("datetime"):
                    date_str = time_tag["datetime"].split("T")[0]
                
                nama_media = "Sinar Lampung"
                # Append ke list
                tanggal_list.append(date_str)
                nama_media_list.append(nama_media)
                judul_list.append(alt)
                link_list.append(href)
                
            # Deteksi pagination
            has_pagination = soup.find("div", class_="flex justify-center mt-8") is not None

            if has_pagination:
                page=page+1
            else:
                break
        else:
            break
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_detiksumbagsel(keyword, max_pages, timeout):
    page = 1
    bulan_map = {
                "Janu": "01", "Feb": "02", "Mar": "03", "Apr": "04",
                "Mei": "05", "Jun": "06", "Jul": "07", "Agu": "08",
                "Sep": "09", "Okt": "10", "Nov": "11", "Des": "12"
    }
    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []
    
    while page <= max_pages:
        url = f"https://www.detik.com/search/searchall?query={keyword}&page={page}&result_type=relevansi&siteid=154"
        response = requests.get(url, timeout)

        
        if response.status_code == 200:
            soup = BeautifulSoup(response.content, 'html.parser')

            for article in soup.select("article.list-content__item"):
                # Judul & Link
                a_tag = article.select_one("h3.media__title a")
                dtr_ttl = a_tag.get("dtr-ttl", "").strip()
                href = a_tag.get("href", "").strip()

                # Nama media (misal di h2.media__subtitle)
                nama_media_tag = article.select_one("h2.media__subtitle")
                nama_media = nama_media_tag.get_text(strip=True) if nama_media_tag else ""

                # Tanggal
                span_tag = article.select_one(".media__date span")
                title_date = span_tag.get("title", "").strip()

                # Parsing tanggal ke YYYY-MM-DD
                parts = title_date.split()
                day = parts[1]
                month = bulan_map.get(parts[2], "01")
                year = parts[3]
                tanggal_format = f"{year}-{month}-{day}"

                # Append ke list
                tanggal_list.append(tanggal_format)
                nama_media_list.append(nama_media)
                judul_list.append(dtr_ttl)
                link_list.append(href)

            # Pengecekan apakah ada halaman selanjutnya
            pagination = soup.find("div", class_="pagination")
            if pagination:
                # Ambil semua link yang punya angka (bukan "Prev" / "Next")
                page_numbers = []
                for a in pagination.find_all("a", class_="itp-pagination"):
                    try:
                        page_numbers.append(int(a.get_text(strip=True)))
                    except ValueError:
                        pass  # kalau bukan angka, skip

                if page_numbers:
                    last_page = max(page_numbers)  # halaman terakhir
                    if page < last_page:
                        page += 1
                    else:
                        break
                else:
                    break
            else:
                break
        else:
            print(f"Error fetching page {page}: {response.status_code}")
            break

    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df
         
def get_search_results_harianlampung(keyword, timeout):
    base_url = f"https://harianlampung.id/?s={keyword}&post_type%5B%5D=post"

    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []

    url = base_url
    response = requests.get(url, timeout)

    if response.status_code == 200:
        soup = BeautifulSoup(response.content, 'html.parser')
        articles = soup.find_all("article", class_="post")

        for art in articles:
            # Judul & Link
            title_tag = art.find("h2", class_="entry-title").a
            title = title_tag.get_text(strip=True)
            link = title_tag["href"]

            # Tanggal
            time_tag = art.find("time", class_="published")
            date_str = time_tag["datetime"] if time_tag else None
            date_formatted = datetime.fromisoformat(date_str.replace("Z", "+00:00")).strftime("%Y-%m-%d") if date_str else None

            nama_media = "Harian Lampung"
            # Append ke list
            tanggal_list.append(date_formatted)
            nama_media_list.append(nama_media)
            judul_list.append(title)
            link_list.append(link)
            
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_harianfajarlampung(keyword, timeout):
    base_url = f"https://harianfajarlampung.co.id/?s={keyword}&post_type=post"

    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []

    url = base_url
    response = requests.get(url, timeout)

    if response.status_code == 200:
        soup = BeautifulSoup(response.content, 'html.parser')

        for article in soup.find_all("article"):
            # tanggal
            tanggal_raw = article.select_one("time")["datetime"]  # ambil datetime dari atribut
            tanggal = datetime.fromisoformat(tanggal_raw.replace("Z", "+00:00")).strftime("%Y-%m-%d")

            # judul & link
            judul_tag = article.select_one("h2.entry-title a")
            judul = judul_tag.get_text(strip=True)
            link = judul_tag["href"]

            nama_media = "Harian Fajar Lampung"
            # Append ke list
            tanggal_list.append(tanggal)
            nama_media_list.append(nama_media)
            judul_list.append(judul)
            link_list.append(link)

    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_serambilampung(keyword, max_pages, timeout):
    page = 1

    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []

    bulan_map = {
    "Januari": "01", "Februari": "02", "Maret": "03", "April": "04",
    "Mei": "05", "Juni": "06", "Juli": "07", "Agustus": "08",
    "September": "09", "Oktober": "10", "November": "11", "Desember": "12"
    }

    while page <= max_pages:
        url = f"https://serambilampung.com/page/{page}/?s={keyword}"
        response = requests.get(url, timeout)

        if response.status_code == 200:
            soup = BeautifulSoup(response.content, 'html.parser')
            articles = soup.find_all("div", class_="category-text-wrap")

            for art in articles:
                a_tag = art.find("h2").find("a")
                title = a_tag.get_text(strip=True)
                link = a_tag["href"]

                # Ambil teks tanggal mentah
                span_tag = art.find("p", class_="category-kategori").find("span")
                raw_date = span_tag.get_text(strip=True)  # "Senin, 11 Agustus 2025  - 17:00 WIB"
                
                # Ambil bagian tanggal saja
                date_part = raw_date.split("-")[0].strip()  # "Senin, 11 Agustus 2025"
                date_only = date_part.split(", ")[-1]       # "11 Agustus 2025"
                
                # Pecah tanggal
                day, month_name, year = date_only.split(" ")
                month_num = bulan_map[month_name]
                
                date_str = f"{year}-{month_num}-{day.zfill(2)}"
                
                nama_media = "Serambi Lampung"
                # Append ke list
                tanggal_list.append(date_str)
                nama_media_list.append(nama_media)
                judul_list.append(title)
                link_list.append(link)
                        
            # Deteksi pagination
            has_pagination = soup.find("div", class_="navigation") is not None

            if has_pagination:
                page=page+1
            else:
                break
        else:
            break
        
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_gemamedia(keyword, max_pages, timeout):
    page = 1

    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []

    bulan_map = {
    "Januari": "01", "Februari": "02", "Maret": "03", "April": "04",
    "Mei": "05", "Juni": "06", "Juli": "07", "Agustus": "08",
    "September": "09", "Oktober": "10", "November": "11", "Desember": "12"
    }

    while page <= max_pages:
        url = f"https://gemamedia.co/page/{page}/?s={keyword}"
        response = requests.get(url, timeout)

        if response.status_code == 200:
            soup = BeautifulSoup(response.content, 'html.parser')
            articles = soup.find_all("article", class_="d-md-flex mg-posts-sec-post")

            for art in articles:
                # Link (ambil dari a.link-div kalau ada, kalau tidak dari h4 a)
                link_tag = art.select_one("a.link-div") or art.select_one("h4.entry-title a")
                link = link_tag.get("href") if link_tag else None
                
                # Judul
                title_tag = art.select_one("h4.entry-title a")
                title = title_tag.get_text(strip=True) if title_tag else None
                
                # Tanggal (format YYYY-MM-DD)
                date_tag = art.select_one("span.mg-blog-date a")
                if date_tag:
                    tanggal_raw = date_tag.get_text(strip=True).replace(",", "")
                    parts = tanggal_raw.split()
                    bulan = bulan_map.get(parts[0], "01")
                    hari = parts[1].zfill(2)
                    tahun = parts[2]
                    tanggal_format = f"{tahun}-{bulan}-{hari}"
                else:
                    tanggal_format = None
                
                nama_media = "Gema Media"
                # Append ke list
                tanggal_list.append(tanggal_format)
                nama_media_list.append(nama_media)
                judul_list.append(title)
                link_list.append(link)
                        
            # Deteksi pagination
            has_pagination = soup.find("div", class_="navigation") is not None

            if has_pagination:
                page=page+1
            else:
                break
        else:
            break
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_infolampung(keyword, max_pages, timeout):
    page = 1

    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []

    while page <= max_pages:
        url = f"https://www.infolampung.id/page/{page}/?s={keyword}"
        response = requests.get(url, timeout)

        if response.status_code == 200:
            soup = BeautifulSoup(response.content, 'html.parser')
            items = soup.select("div.category-text-wrap")

            for item in items:
                # Judul & Link
                a_tag = item.select_one("h2 a")
                judul = a_tag.get_text(strip=True) if a_tag else None
                link = a_tag["href"] if a_tag else None

                # Tanggal
                tgl_tag = item.select_one("div.tanggal-mobile")
                if tgl_tag:
                    tgl_raw = tgl_tag.get_text(strip=True)
                    tgl_parts = tgl_raw.split("-")[0].strip()  # "Wednesday, 16 July 2025"
                    tgl_parts = " ".join(tgl_parts.split()[1:])  # buang hari → "16 July 2025"
                    tgl_obj = datetime.strptime(tgl_parts, "%d %B %Y")
                    tgl_format = tgl_obj.strftime("%Y-%m-%d")
                else:
                    tgl_format = None
                
                nama_media = "Info Lampung"
                # Append ke list
                tanggal_list.append(tgl_format)
                nama_media_list.append(nama_media)
                judul_list.append(judul)
                link_list.append(link)
                        
            # Deteksi pagination
            has_pagination = soup.find("div", class_="navigation") is not None

            if has_pagination:
                page=page+1
            else:
                break
        else:
            break
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_lampungdalamberita(keyword, max_pages, timeout):
    page = 1
    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []

    while page <= max_pages:
        url = f"https://lampungdalamberita.com/page/{page}/?s={keyword}"
        response = requests.get(url, timeout)

        if response.status_code == 200:
            soup = BeautifulSoup(response.content, 'html.parser')
            articles = soup.find_all("article", class_="hentry")

            for art in articles:
                # Judul & link
                title_tag = art.find("h2", class_="post-title").find("a")
                title = title_tag.get_text(strip=True)
                link = title_tag["href"]

                # Tanggal
                date_tag = art.find("span", class_="updated")
                if date_tag:
                    raw_date = date_tag.get_text(strip=True)  # contoh: "Sep 18, 2023"
                    date_obj = datetime.strptime(raw_date, "%b %d, %Y")
                    date_str = date_obj.strftime("%Y-%m-%d")
                else:
                    date_str = None
                
                nama_media = "Lampung Dalam Berita"
                # Append ke list
                tanggal_list.append(date_str)
                nama_media_list.append(nama_media)
                judul_list.append(title)
                link_list.append(link)
                        
            # Deteksi pagination
            has_pagination = soup.find("div", class_="archive-pagination") is not None

            if has_pagination:
                page=page+1
            else:
                break
        else:
            break
    
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def get_search_results_katalampung(keyword, max_pages, timeout):
    page = 0

    # List penampung data
    tanggal_list = []
    nama_media_list = []
    judul_list = []
    link_list = []
    
    bulan_map = {
    "Januari": "01", "Februari": "02", "Maret": "03", "April": "04",
    "Mei": "05", "Juni": "06", "Juli": "07", "Agustus": "08",
    "September": "09", "Oktober": "10", "November": "11", "Desember": "12"
    }

    maxp = max_pages*20
    while page <= maxp:
        url = f"https://www.katalampung.com/search?q={keyword}&max-results=20&start={page}&by-date=false"
        headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36"
        }
        response = requests.get(url, headers=headers, timeout=timeout)
        if response.status_code == 200:
            soup = BeautifulSoup(response.content, 'html.parser')
            posts = soup.find_all("div", class_="post-outer")
            for post in posts:
                title_tag = post.find("h2", class_="post-title").a
                title = title_tag.get_text(strip=True)
                link = title_tag["href"]

                # Tanggal
                date_tag = post.find("abbr", class_="published")
                if date_tag:
                    raw_date = date_tag.get_text(strip=True)
                    # Pecah tanggal format "Agustus 06, 2022"
                    parts = raw_date.replace(",", "").split()
                    bulan = bulan_map.get(parts[0], "01")
                    day = parts[1].zfill(2)
                    year = parts[2]
                    tanggal_fix = f"{year}-{bulan}-{day}"
                else:
                    tanggal_fix = None
                
                nama_media = "Kata Lampung"
                # Append ke list
                tanggal_list.append(tanggal_fix)
                nama_media_list.append(nama_media)
                judul_list.append(title)
                link_list.append(link)
                        
            # Deteksi pagination
            has_pagination = soup.find("div", class_="blog-pager") is not None
            if has_pagination:
                page=page+20
            else:
                break
        else:
            break
    # Buat DataFrame
    data = {
        'Tanggal': tanggal_list,
        'Nama Media': nama_media_list,
        'Judul': judul_list,
        'Link': link_list
    }
    df = pd.DataFrame(data, columns=['Tanggal', 'Nama Media', 'Judul', 'Link'])

    return df

def insert_news_to_db(judul, tanggal_berita, link, sumber):
    """Insert satu berita ke MySQL database jika belum ada link yang sama."""
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="siger-lampung"
        )
        cursor = conn.cursor()

        # Cek apakah link sudah ada
        cursor.execute("SELECT COUNT(*) FROM news WHERE link = %s", (link,))
        (count,) = cursor.fetchone()
        if count > 0:
            # print(f"Skip insert, link sudah ada: {link}")
            return  # keluar dari fungsi tanpa insert

        # Kalau belum ada, insert
        query = """
        INSERT INTO news (nama, tanggal_berita, tanggal_update, link, sumber)
        VALUES (%s, %s, %s, %s, %s)
        """
        values = (
            judul,                                # nama
            tanggal_berita,                       # tanggal_berita
            datetime.now().strftime("%Y-%m-%d"),  # tanggal_update (hari ini)
            link,                                 # link
            sumber                                # sumber
        )

        cursor.execute(query, values)
        conn.commit()
        # print(f"Berhasil insert: {judul}")

    except mysql.connector.Error as err:
        print(f"Error: {err}")
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def main():
    keyword = "DTSEN"
    max_pages = 10
    retries = 3
    timeout = 40

    df_antaranews = get_search_results_antaranews(keyword, max_pages, retries, timeout)
    df_viva = get_search_results_viva(keyword, max_pages, retries, timeout)
    df_lampungpost = get_search_results_lampungpost(keyword, max_pages, timeout)
    df_sinarlampung = get_search_results_sinarlampung(keyword, max_pages, timeout)
    df_detiksumbagsel = get_search_results_detiksumbagsel(keyword, max_pages, timeout)
    df_harianlampung = get_search_results_harianlampung(keyword, timeout)
    df_harianfajarlampung = get_search_results_harianfajarlampung(keyword, timeout)
    df_serambilampung = get_search_results_serambilampung(keyword, max_pages, timeout)
    df_gemamedia = get_search_results_gemamedia(keyword, max_pages, timeout)
    df_infolampung = get_search_results_infolampung(keyword, max_pages, timeout)
    df_lampungdalamberita = get_search_results_lampungdalamberita(keyword, max_pages, timeout)
    df_katalampung = get_search_results_katalampung(keyword, max_pages, timeout)

    df_list = [
        df_antaranews, df_viva, df_lampungpost, df_sinarlampung, df_detiksumbagsel,
        df_harianlampung, df_harianfajarlampung, df_serambilampung, df_gemamedia,
        df_infolampung, df_lampungdalamberita, df_katalampung
    ]

    df_nonempty = [df for df in df_list if len(df) > 0]

    if df_nonempty:
        df_final = pd.concat(df_nonempty, ignore_index=True)
    else:
        df_final = pd.DataFrame(columns=["Tanggal", "Nama Media", "Judul", "Link"])

    df_final = df_final.drop_duplicates(subset=['Link'])
    df_final["Tanggal"] = pd.to_datetime(df_final["Tanggal"], errors="coerce")
    df_final = df_final.sort_values(by="Tanggal", ascending=False)
    df_final = df_final.reset_index(drop=True)

    # Masukkan ke DB
    for _, row in df_final.iterrows():
        if pd.isnull(row["Tanggal"]):
            tanggal_str = None
        else:
            tanggal_str = row["Tanggal"].strftime("%Y-%m-%d")
        insert_news_to_db(
            judul=row["Judul"],
            tanggal_berita=tanggal_str,
            link=row["Link"],
            sumber=row["Nama Media"]
        )

    print(f"{len(df_final)} berita berhasil diproses & dimasukkan ke DB.")

# =======================
# Jalankan script
# =======================
if __name__ == "__main__":
    main()
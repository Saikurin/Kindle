import requests
import mysql.connector as mysql
import re
import json as JSON


def getbooks(page):
    response = requests.get(f'https://gutendex.com/books/?page={page}')
    if response.status_code == 200:
        return response.json()['results']
    else:
        return None


key = 0
page = 1
bookAlreadyTaken = 0
maxBook = 100
# 52

db = mysql.connect(
    host="localhost",
    user="symfony",
    passwd="symfony",
    database="symfony_api"
)

while bookAlreadyTaken < maxBook:
    books = getbooks(page)
    # Get only maxBook books
    if len(books) > maxBook:
        books = books[key:maxBook]
    else:
        if maxBook - bookAlreadyTaken < len(books):
            books = books[key:maxBook - bookAlreadyTaken]
        else:
            books = books[key:len(books)]

    if key <= len(books):
        db.close()
        for book in books:
            db = mysql.connect(
                host="localhost",
                user="symfony",
                passwd="symfony",
                database="symfony_api"
            )

            id = book['id']
            print("Livre en cours de traitement : ", book['title'])
            cursor = db.cursor()
            cursor.execute("SELECT * FROM book WHERE gut_id = %s and already_index = 1", (id,))
            result = cursor.fetchall()
            if len(result) > 0:
                bookAlreadyTaken += 1
                continue

            cursor.execute("INSERT IGNORE INTO book (gut_id, name, metadata, already_index) VALUES (%s, %s, %s, %s)",
                           (id, book['title'], JSON.dumps(book), 0))
            bookTitle = book['title']
            db.commit()
            res = requests.get(f"https://www.gutenberg.org/files/{id}/{id}-0.txt")
            if res.status_code == 200:
                content = res.text

                # Trouver tous les mots dans la chaîne de caractères
                words = re.findall(r'\w+', content)

                # Exclure les mots qui ne contiennent pas uniquement des lettres
                filtered_words = [word.lower() for word in words if len(word) > 2 and word.isalpha()]

                occurences = {}
                for word in filtered_words:
                    if word in occurences:
                        occurences[word] += 1
                    else:
                        occurences[word] = 1

                unique_words = list(set(filtered_words))

                for word in unique_words:
                    cursor.execute(f"SELECT * FROM indexes WHERE token = '{word}'")
                    result = cursor.fetchall()
                    if len(result) == 0:
                        cursor.execute('INSERT INTO indexes (token, metadata) VALUES (%s, %s)',
                                       (word, "[{\"id\": " + str(id) + ", \"occurences\": " + str(
                                           occurences[word]) + "}]"))
                        db.commit()
                    else:
                        json = JSON.loads(result[0][2])
                        for book in json:
                            if book["id"] != id:
                                json.append({"id": id, "occurences": occurences[word]})
                                try:
                                    cursor.execute("UPDATE indexes SET metadata = %s WHERE token = %s",
                                                   (JSON.dumps(json), word))
                                    db.commit()
                                except mysql.errors.DataError as e:
                                    print(e)
                                    print(word)
                                    exit(1)
                                break
            else:
                print("Livre non traité : ", bookTitle)
                print("Erreur : ", res.status_code)
            db.cursor().execute("UPDATE book SET already_index = 1 WHERE gut_id = %s", (id,))
            db.commit()
            key += 1
            bookAlreadyTaken += 1
    key = 0
    page += 1


# Algorithme de suggestion
cursor = db.cursor()
cursor.execute("SELECT * FROM book where already_index = 1 ORDER BY gut_id")
books = cursor.fetchall()
if len(books) > 0:
    for book in books:
        subjects = JSON.loads(book[3]).get('subjects')[0:2]
        if len(subjects) > 0:
            for subject in subjects:
                print(subject)
                # GET BOOKS FROM GUTENDEX WITH SUBJECT
                response = requests.get(f'https://gutendex.com/books/?topic={subject}')
                booksPerSubject = response.json()['results'][0:2]
                for bookPerSubject in booksPerSubject:
                    cursor.execute("SELECT COUNT(*) FROM book where gut_id = %s", (bookPerSubject['id'],))
                    result = cursor.fetchall()
                    if len(result) == 0:
                        continue
                    cursor.execute("INSERT INTO book_suggestions (book_source, book_target) VALUES (%s, (SELECT id "
                                   "from book where gut_id = %s))", (book[0], bookPerSubject['id']))
                    db.commit()
        else:
            print("Pas de sujet")
print("Fin du script")

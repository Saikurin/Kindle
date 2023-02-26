import mysql.connector as mysql
import json as JSON
import numpy as np
import networkx as nx

db = mysql.connect(
    host="*******",
    user="*******",
    passwd="*****",
    database="*****"
)

cursor = db.cursor()
cursor.execute("SELECT * FROM book where already_index = 1")
books = cursor.fetchall()

cursor.execute("SELECT * FROM indexes")
tokens = cursor.fetchall()
jacard = np.array(np.zeros((len(books), len(books))))
indexBook = 0
seuil = 0.85
# Fabrication de la matrice en utilisant la formule de Jacard
for book in books:
    indexAnotherBook = 0
    for another_book in books:
        if book[0] == another_book[0]:
            jacard[0][0] = 0
            indexAnotherBook += 1
            continue
        X = 0
        Y = 0
        for token in tokens:
            books_token = JSON.loads(token[2])
            K1 = 0
            K2 = 0
            for book_token in books_token:
                if book_token["id"] == book[1] or book_token["id"] == another_book[1]:
                    if book_token["id"] == book[1]:
                        K1 = book_token["occurences"]
                    else:
                        if book_token["id"] == another_book[1]:
                            K2 = book_token["occurences"]
            X += max(K1, K2) - min(K1, K2)
            Y += max(K1, K2)
        if Y == 0:
            jacard[indexBook][indexAnotherBook] = 0
            continue
        jacard[indexBook][indexAnotherBook] = X / Y
        indexAnotherBook += 1
    indexBook += 1

# Suppresion des liens trop eloignés par rapport au seuil
for i in range(len(jacard)):
    for j in range(len(jacard)):
        if jacard[i][j] > seuil:
            jacard[i][j] = 0

print('Fin de jacard')

# print(jacard)
# Création du graphe
G = nx.from_numpy_array(jacard)
betweenness = nx.betweenness_centrality(G, normalized=True, endpoints=True)
betweennessJSON = JSON.dumps(betweenness)
for idBook, score in betweenness.items():
    cursor.execute(f"UPDATE book SET score = {score} WHERE id = {books[idBook][0]}")
    db.commit()

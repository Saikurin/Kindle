import time

import mysql.connector as mysql
import requests as req
import re
import json as JSON


# Arbre de trie
class Node:
    def __init__(self, value=None):
        self.value = value
        self.children = {}


def insert_word(node, word):
    if len(word) == 0:
        return

    if word[0] not in node.children:
        node.children[word[0]] = Node(word[0])

    insert_word(node.children[word[0]], word[1:])


def build_prefix_tree(words):
    root = Node()
    for word in words:
        insert_word(root, word)

    return root


def prefix_tree_to_json(node):
    if node.value is not None:
        json = {"0": node.value, "children": []}
        for child in node.children.values():
            json["children"].append(prefix_tree_to_json(child))
        return json
    else:
        json = {"children": []}
        for child in node.children.values():
            json["children"].append(prefix_tree_to_json(child))
        return json

# Connect to the database
db = mysql.connect(
    host="*****",
    user="*****",
    passwd="*****",
    database="*****"
)

db.cursor().execute(
    "create table if not exists `indexes`(id int auto_increment primary key,token varchar(255) not null unique,metadata json not null)")

db.cursor().execute(
    "create table if not exists `tree` (id int auto_increment primary key,token varchar(255) not null unique,tree json not null)")

db.close()

'''
    Boucle toutes les 5 secondes tant qu'il n'y a pas d'insertion de nouveaux livres
'''
while True:
    db = mysql.connect(
        host="*****",
        user="*****",
        passwd="*****",
        database="*****"
    )
    cursor = db.cursor()
    cursor.execute("SELECT * FROM book where already_index = 0")
    data = cursor.fetchall()

    if len(data) > 0:
        for book in data:
            print("Nouveau livre détécté : ", book[2])
            id = book[1]
            ''' 
                Récupération du contenu du livre et traitement
            '''
            response = req.get(f"https://www.gutenberg.org/files/{id}/{id}-0.txt")
            if response.status_code == 200:
                content = response.text

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

            db.cursor().execute("UPDATE book SET already_index = 1 WHERE gut_id = %s", (id,))
            '''
                Recréation de la table qui contient l'arbre
            '''
            cursor.execute(f"SELECT DISTINCT token FROM indexes")
            tokens = cursor.fetchall()

            prefix_tree = build_prefix_tree([token[0] for token in tokens])
            json_tree = prefix_tree_to_json(prefix_tree)

            cursor.execute("TRUNCATE TABLE tree")
            try:
                cursor.execute("insert into tree (token, tree) values (%s, %s)", ("root", JSON.dumps(json_tree)))
                db.commit()
            except mysql.errors.IntegrityError as e:
                print(e)
                exit(1)

        print("Fin du livre")
    else:
        print("Pas de nouveau livre")

    db.close()
    time.sleep(5)


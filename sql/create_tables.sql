CREATE TABLE IF NOT EXISTS users (
                                     id INT PRIMARY KEY,
                                     name TEXT
);

CREATE TABLE IF NOT EXISTS posts (
                                     id INT PRIMARY KEY,
                                     userId INT,
                                     title TEXT,
                                     body TEXT
);

CREATE TABLE IF NOT EXISTS comments (
                                        id INT PRIMARY KEY,
                                        postId INT,
                                        name TEXT,
                                        email TEXT,
                                        body TEXT,
                                        FOREIGN KEY (postId) REFERENCES posts(id)
    );
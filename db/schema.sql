CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL
);
DELETE FROM usuarios;

CREATE TABLE IF NOT EXISTS bairros (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS tipos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS imoveis (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT NOT NULL,
    descricao TEXT,
    tipo_id INTEGER NOT NULL,
    bairro_id INTEGER NOT NULL,
    quartos INTEGER NOT NULL,
    preco REAL NOT NULL,
    FOREIGN KEY (tipo_id) REFERENCES tipos(id),
    FOREIGN KEY (bairro_id) REFERENCES bairros(id)
);

CREATE TABLE IF NOT EXISTS interesses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    telefone TEXT NOT NULL,
    tipo_id INTEGER NOT NULL,
    min_preco REAL,
    max_preco REAL,
    num_quartos INTEGER
);

CREATE TABLE IF NOT EXISTS interesses_bairros (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    interesse_id INTEGER NOT NULL,
    bairro_id INTEGER NOT NULL,
    FOREIGN KEY (interesse_id) REFERENCES interesses(id),
    FOREIGN KEY (bairro_id) REFERENCES bairros(id)
);

INSERT OR IGNORE INTO usuarios (nome, email, senha) VALUES 
-- Senha com bcrypt. Valor original: "admin123"
("Admin", "admin@gmail.com", "$2b$12$tNoES9g.ZmJoLfxGxuJJ6eJbVpF5k5HHIOBfyiKL.8l7TJwYo0MQK");

INSERT OR IGNORE INTO bairros (nome) VALUES 
("Centro"),
("Catete"),
("Gloria"),
("Lapa"),
("Copacabana"),
("Ipanema"),
("Leblon");

INSERT OR IGNORE INTO tipos (nome) VALUES 
("Apartamento"),
("Casa"),
("Cobertura"),
("Kitnet"),
("Studio");

INSERT OR IGNORE INTO imoveis (titulo, descricao, tipo_id, bairro_id, quartos, preco) VALUES 
("Apartamento no Centro", "Apartamento com 2 quartos e 1 banheiro.", 1, 1, 2, 300000),
("Casa na Lapa", "Casa com 3 quartos e 2 banheiros.", 2, 4, 3, 500000),
("Cobertura em Copacabana", "Cobertura com vista para o mar.", 3, 5, 4, 1500000),
("Kitnet em Ipanema", "Kitnet próximo à praia.", 4, 6, 1, 200000),
("Studio no Leblon", "Studio moderno e bem localizado.", 5, 7, 1, 800000);

INSERT OR IGNORE INTO interesses (nome, telefone, tipo_id, min_preco, max_preco, num_quartos) VALUES 
("João", "21987654321", 1, 200000, 500000, 2),
("João", "21987654322", 2, 300000, 800000, 3),
("Pedro", "21987654323", 3, 1000000, 2000000, 4),
("Ana", "21987654324", 4, 150000, 300000, 1),
("Fernando", "21987654325", 5, 500000, 1000000, 2);

INSERT OR IGNORE INTO interesses_bairros (interesse_id, bairro_id) VALUES 
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 6);

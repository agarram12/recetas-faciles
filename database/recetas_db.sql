CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255)
);

CREATE TABLE recetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    categoria_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT DEFAULT NULL,
    pasos TEXT,
    url_imagen VARCHAR(255) DEFAULT 'assets/img/logo.png',
    tiempo_coccion INT NOT NULL,
    dificultad ENUM('Fácil', 'Media', 'Difícil') DEFAULT 'Media',
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

CREATE TABLE ingredientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE receta_ingredientes (
    receta_id INT NOT NULL,
    ingrediente_id INT NOT NULL,
    cantidad VARCHAR(50),
    PRIMARY KEY (receta_id, ingrediente_id),
    FOREIGN KEY (receta_id) REFERENCES recetas(id) ON DELETE CASCADE,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id) ON DELETE CASCADE
);

CREATE TABLE comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    receta_id INT NOT NULL,
    contenido TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receta_id) REFERENCES recetas(id) ON DELETE CASCADE
);

CREATE TABLE valoraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    receta_id INT NOT NULL,
    puntuacion INT CHECK (puntuacion BETWEEN 1 AND 5),
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receta_id) REFERENCES recetas(id) ON DELETE CASCADE
);

CREATE TABLE favoritos (
    usuario_id BIGINT UNSIGNED NOT NULL,
    receta_id INT NOT NULL,
    fecha_guardado DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, receta_id),
    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receta_id) REFERENCES recetas(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, descripcion) VALUES 
('Antonio Cocinitas', 'antonio@email.com', '$2y$10$eNY72D/UErU3AH2AEMm9h.l1STMRM5LoN/r4NcEMOz6bongzaGOl2', 'Amante de lo tradicional.'),
('María Chef', 'maria@email.com', '$2y$10$eNY72D/UErU3AH2AEMm9h.l1STMRM5LoN/r4NcEMOz6bongzaGOl2', 'Especialista en postres.'),
('VeganLife', 'vegan@email.com', '$2y$10$eNY72D/UErU3AH2AEMm9h.l1STMRM5LoN/r4NcEMOz6bongzaGOl2', 'Recetas 100% plant-based.'),
('Carlos Parrilla', 'carlos@email.com', '$2y$10$eNY72D/UErU3AH2AEMm9h.l1STMRM5LoN/r4NcEMOz6bongzaGOl2', 'Loco por la carne y las brasas.'),
('Laura Saludable', 'laura@email.com', '$2y$10$eNY72D/UErU3AH2AEMm9h.l1STMRM5LoN/r4NcEMOz6bongzaGOl2', 'Nutrición y sabor van de la mano.');

INSERT INTO categorias (nombre, descripcion) VALUES 
('Veganos', 'Recetas 100% plant-based y saludables.'),
('Carnívoros', 'Las mejores recetas para amantes de la carne.'),
('Dulceros', 'Postres y delicias para los más golosos.');

INSERT INTO recetas (usuario_id, categoria_id, titulo, pasos, url_imagen, tiempo_coccion, dificultad) VALUES 
(1, 1, 'Tortilla de Patatas', 'Freír patatas y mezclar con huevo.', 'assets/img/tortilla.jpg', 40, 'Media'),
(2, 3, 'Tarta de Queso', 'Triturar galletas y mezclar con queso.', 'assets/img/cheesecake.jpg', 50, 'Fácil'),
(3, 1, 'Ensalada Fresca', 'Lavar lechuga, cortar tomate y aliñar.', 'assets/img/salad.jpg', 10, 'Fácil'),
(4, 2, 'Costillas BBQ', 'Adobar y hornear a baja temperatura.', 'assets/img/bbq.jpg', 120, 'Media'),
(5, 1, 'Gazpacho Andaluz', 'Triturar tomate, pimiento, pepino y ajo.', 'assets/img/gazpacho.jpg', 15, 'Fácil');

INSERT INTO ingredientes (nombre) VALUES 
('Patata'), ('Huevo'), ('Queso Crema'), ('Tomate'), ('Costilla de Cerdo');

INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad) VALUES 
(1, 1, '4 unidades'), (1, 2, '6 unidades'), (2, 3, '500 gramos'), (5, 4, '1 kilo'), (4, 5, '1 costillar');

INSERT INTO comentarios (usuario_id, receta_id, contenido) VALUES 
(2, 1, 'Me encanta.'), (3, 5, 'Perfecto para verano.'), (1, 4, 'Buena salsa.'), (4, 2, 'Voló rápido.'), (5, 3, 'Sana y ligera.');

INSERT INTO valoraciones (usuario_id, receta_id, puntuacion) VALUES 
(2, 1, 5), (3, 5, 5), (1, 4, 4), (4, 2, 5), (5, 3, 4);

INSERT INTO favoritos (usuario_id, receta_id) VALUES 
(1, 4), (2, 1), (3, 5), (4, 2), (5, 3);
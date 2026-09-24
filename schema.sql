-- Banco de dados: Site de Farmácia
CREATE DATABASE IF NOT EXISTS farmacia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE farmacia;

CREATE TABLE IF NOT EXISTS medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    categoria VARCHAR(80) NOT NULL,
    fabricante VARCHAR(120) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    precisa_receita TINYINT(1) NOT NULL DEFAULT 0,
    imagem VARCHAR(255) DEFAULT 'sem-imagem.png',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    senha_hash VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    medicamento_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    valor_total DECIMAL(10,2) NOT NULL,
    status ENUM('Pendente','Pago','Cancelado','Entregue') NOT NULL DEFAULT 'Pendente',
    data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id) ON DELETE CASCADE
);

-- Dados de exemplo
INSERT INTO medicamentos (nome, categoria, fabricante, descricao, preco, estoque, precisa_receita) VALUES
('Paracetamol 750mg', 'Analgésico', 'EMS', 'Alívio de dores e febre.', 12.50, 100, 0),
('Amoxicilina 500mg', 'Antibiótico', 'Medley', 'Antibiótico de amplo espectro.', 28.90, 40, 1),
('Dipirona Sódica', 'Analgésico', 'Neo Química', 'Combate dores e febre.', 9.90, 80, 0),
('Omeprazol 20mg', 'Gástrico', 'Eurofarma', 'Reduz acidez estomacal.', 15.30, 60, 0),
('Losartana 50mg', 'Cardiovascular', 'Germed', 'Controle da pressão arterial.', 22.00, 35, 1);

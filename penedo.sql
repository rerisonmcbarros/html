create database if not exists penedo;

use penedo;

create table if not exists categoria(
	id int not null primary key,
    codigo varchar(30) not null unique,
    nome text not null
);

create table if not exists produto(
	id int not null primary key,
    codigo varchar(30) not null unique,
    id_categoria int not null references categoria.id,
	descricao text not null,
    preco_custo float not null,
    preco_venda float not null,
    estoque int not null
);

create table if not exists venda(
	id int not null primary key,
    nome_cliente text not null,
	valor_total float not null,
    desconto int not null,
    pagamento text not null,
    data_venda datetime default current_timestamp
); 

create table if not exists item_venda(
	id int not null primary key,
    id_venda int not null references venda.id,
    id_produto int not null references produto.id,
    item_preco float not null,
    quantidade int not null
);

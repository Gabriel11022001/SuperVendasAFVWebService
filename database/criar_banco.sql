-- criar tabela de clientes
create table tb_clientes(
	cliente_id serial primary key,
	tipo_pessoa text not null,
	telefone_principal text not null,
	telefone_secundario text,
	email_principal text not null,
	email_secundario text,
	status boolean not null default true,
	nome_completo text,
	cpf text,
	data_nascimento date,
	genero text,
	tipo_documento text,
	numero_documento text,
	nome_mae text,
	nome_pai text,
	razao_social text,
	cnpj text,
	data_fundacao date,
	valor_patrimonio decimal
);

-- criar tabela de endereços dos clientes
create table tb_enderecos(
	endereco_id serial primary key,
	cep text not null,
	complemento text,
	logradouro text not null,
	bairro text not null,
	cidade text not null,
	uf text not null,
	numero text not null default 's/n',
	cliente_id integer,
	foreign key(cliente_id) references tb_clientes(cliente_id)
);
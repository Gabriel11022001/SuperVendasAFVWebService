-- criar tabela de nivel de acesso
create table tb_niveis_acesso (
	nivel_acesso_id serial primary key,
	nome text not null,
	status boolean default true
);

-- criar tabela de permissõs dos niveis de acesso
create table tb_permissoes(
	permissao_id serial primary key,
	nome text not null,
	status boolean default true
);

-- criar tabela pivo entre permissões e niveis de acesso
create table tb_permissao_nivel_acesso(
	nivel_acesso_id integer not null,
	permissao_id integer not null
);

-- criar tabela de usuários
create tabela tb_usuarios (
	usuario_id serial primary key,
	nome_completo text not null,
	login text not null,
	senha text not null,
	email text not null,
	data_cadastro date not null,
	status text not null default 'habilitado',
	nivel_acesso_id integer not null,
	foreign key(nivel_acesso_id) references tb_niveis_acesso(nivel_acesso_id)
);

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
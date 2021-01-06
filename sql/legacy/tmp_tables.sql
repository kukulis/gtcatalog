drop table if exists tmp_full_products;
create table tmp_full_products
(
	sku varchar(32) not null primary key,
	last_update datetime,
	version int default 0,
	parent_sku varchar(32) null,
	origin_country_code varchar(3) null,
	color varchar(255) null,
	for_male tinyint(1) default 0,
	for_female tinyint(1) default 0,
	size varchar(255) null,
	pack_size varchar(255) null,
	pack_amount varchar(255) null,
	weight decimal(10,2) default 0.00,
	length decimal(10,2) default 0.00,
	height decimal(10,2) default 0.00,
	width decimal(10,2) default 0.00,
	delivery_time varchar(255) null,
	info_provider varchar(64) null,
	brand varchar(64) null,
	line varchar(64) null,
	vendor varchar(64) null,
	manufacturer varchar(64) null,
	type varchar(64) null,
	purpose varchar(64) null,
	measure varchar(64) null,
	productgroup varchar(64) null,
	deposit_code varchar(32) null,
	code_from_custom varchar(32) null,
	guaranty varchar(64) null,
	code_from_supplier varchar(32) null,
	code_from_vendor varchar(32) null,
	priority varchar(32) null,
	google_product_category_id int null
);

drop table if exists tmp_full_products_languages;

create table tmp_full_products_languages
(
	sku varchar(32) not null,
	language varchar(2) not null,
	name varchar(255) not null,
	description longtext null,
	label longtext null,
	variant_name varchar(255) null,
	info_provider varchar(64) null,
	tags text null,
	label_size varchar(32) null,
	distributor varchar(64) null,
	composition text null,
	primary key (sku, language)
);

drop table if exists  tmp_products_pictures;
create table tmp_products_pictures
(
	priority int null,
	sku varchar(32) not null,
	picture_id int null,
	legacy_id varchar(64) primary key ,
	url varchar(255),
	name varchar (255),
    info_provider varchar(64),
    statusas varchar(16)
);


drop table if exists  tmp_products_categories;
create table tmp_products_categories (
    category varchar(64) not null,
    parent varchar(64) null,
    sku varchar(32) not null,
    language varchar(2) not null,
    name varchar(255) not null,
    description longtext null,
    primary key (category, language, sku)
);

drop table if exists tmp_classificators;

create table tmp_classificators (
    language_code varchar(2) not null,
    classificator_code varchar(64) not null,
    group_code varchar(64) not null,
    value varchar(255) not null,
    primary key (language_code, classificator_code)
);


alter table tmp_products_categories
add column depth int null;


create index tmp_products_categories_depth_idx
    on tmp_products_categories (depth);

create index tmp_products_categories_parent_idx
    on tmp_products_categories (parent);

create index tmp_products_categories_category_idx
    on tmp_products_categories (category);



-- alter table tmp_products_pictures drop column is_uploaded;
alter table tmp_products_pictures add column
is_downloaded tinyint null ;

create index tmp_products_pictures_sku_idx
on tmp_products_pictures (sku);




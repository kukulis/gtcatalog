create table tmp_skus
(
	sku varchar(32) not null
		primary key
);

create or replace table tmp_skus1
(
    sku varchar(32) not null
        primary key
);


insert ignore into tmp_skus1 select sku from tmp_skus where sku REGEXP  '^[[:alnum:]-]+$'
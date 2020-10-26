create table tmp_skus
(
	sku varchar(32) not null
		primary key
);


insert into tmp_skus select nomnr from tmp_preke where nomnr is not null;
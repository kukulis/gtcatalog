-- 1 classificators groups
insert ignore into classificators_groups ( code, name)
select distinct group_code, group_code from tmp_classificators;

-- 2 classificators
insert ignore into classificators (code, group_code) select classificator_code, group_code from tmp_classificators;


-- select * from classificators where code='-'
-- 3 classificators languages
insert ignore into classificator_lang (language_code, classificator_code, value) SELECT language_code, classificator_code, value from tmp_classificators;

-- 4 products
insert ignore into products (
sku,
last_update,
version,
parent_sku,
origin_country_code,
color,
for_male,
for_female,
size,
pack_size,
pack_amount,
weight,
length,
height,
width,
delivery_time,
info_provider,
brand,
line,
vendor,
manufacturer,
type,
purpose,
measure,
productgroup,
deposit_code,
code_from_custom,
guaranty,
code_from_supplier,
code_from_vendor,
priority,
google_product_category_id
) select
    sku,
    last_update,
    version,
    parent_sku,
    origin_country_code,
    color,
    for_male,
    for_female,
    size,
    pack_size,
    pack_amount,
    weight,
    length,
    height,
    width,
    delivery_time,
    info_provider,
    nullif(brand, ''),
    nullif(line, ''),
    nullif(vendor,''),
    nullif(manufacturer,''),
    nullif(type,''),
    nullif(purpose,''),
    nullif(measure,''),
    nullif(productgroup,''),
    deposit_code,
    code_from_custom,
    guaranty,
    code_from_supplier,
    code_from_vendor,
    priority,
    google_product_category_id
from tmp_full_products;

-- 5 product lang
insert ignore into products_languages
(
    sku,
    language,
    name,
    description,
    label,
    variant_name,
    info_provider,
    tags,
    label_size,
    distributor,
    composition
)
select
    sku,
    language,
    name,
    description,
    label,
    variant_name,
    info_provider,
    tags,
    label_size,
    distributor,
    composition
from tmp_full_products_languages;

# select * from products_languages


-- 6 categories

update tmp_products_categories set parent=null
where parent = '';

select count(1) from tmp_products_categories where parent is null;

update tmp_products_categories set depth=0 where parent is null;

-- one time
update tmp_products_categories
    c_child join tmp_products_categories c_parent
    on c_child.parent = c_parent.category
    set c_child.depth=c_parent.depth+1
    where c_parent.depth is not null;

-- second time
update tmp_products_categories
    c_child join tmp_products_categories c_parent
    on c_child.parent = c_parent.category
set c_child.depth=c_parent.depth+1
where c_parent.depth is not null;

-- third time
update tmp_products_categories
    c_child join tmp_products_categories c_parent
    on c_child.parent = c_parent.category
set c_child.depth=c_parent.depth+1
where c_parent.depth is not null;

-- fourth time
update tmp_products_categories
    c_child join tmp_products_categories c_parent
    on c_child.parent = c_parent.category
set c_child.depth=c_parent.depth+1
where c_parent.depth is not null;

-- fifth time ( gal geriau pasidaryti procedūrą )
update tmp_products_categories
    c_child join tmp_products_categories c_parent
    on c_child.parent = c_parent.category
set c_child.depth=c_parent.depth+1
where c_parent.depth is not null;


insert ignore into categories (
    code,
    parent
    )
    select category, parent from tmp_products_categories
    where depth is not null
    order by depth
;

-- tos kurios neturi parent atitikmens, įterpiam be parent
insert ignore into categories (
    code,
    parent
)
select category, null from tmp_products_categories
where depth is null;

# select * from tmp_products_categories order by depth desc;
-- 7 categories langs
insert ignore into categories_languages (
    category,
    language,
    name,
    description
) select
      category,
      language,
      name,
      description
from tmp_products_categories;

# select * from categories_languages
-- 8 product categories
insert ignore into products_categories (
    priority,
        sku,
    category
)
select
    ifnull(depth, 0), -- nelabai korektiška bet kol kas ok gal reikia priority įdėti pagal tai kokiu eiliškumu buvo masyve iš resto?
    sku,
    category
from tmp_products_categories;

-- 9 images ?
insert ignore into pictures
    (name, reference)
    select name, legacy_id
from tmp_products_pictures
where legacy_id is not null;

-- update images ids from the
update tmp_products_pictures tp join pictures p
on tp.legacy_id = p.reference
set tp.picture_id = p.id;

-- select * from tmp_products_pictures where picture_id is null;
-- 10 product images ?
insert into products_pictures (
priority,
sku,
picture_id)
select tpp.priority, tpp.sku, tpp.picture_id
from tmp_products_pictures tpp join products p on tpp.sku = p.sku
where picture_id is not null;


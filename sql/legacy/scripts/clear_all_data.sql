truncate categories_languages;
truncate products_languages;
truncate products_categories;
truncate products_pictures;
update categories set parent=null;
delete from categories;
delete from pictures;
-- pratrinti direktorijas

delete from classificator_lang;
delete from products;
delete from classificators;

delete from classificators_groups where code in ( 'brand', 'line', 'manufacturer', 'vendor' );
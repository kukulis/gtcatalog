update tmp_products_categories set parent=null where parent='';

update tmp_categories set parent=null where parent='';

insert into tmp_categories ( category, parent, depth)
select category, parent, null from tmp_products_categories
on duplicate  key update tmp_categories.parent=ifnull(tmp_categories.parent, tmp_products_categories.parent);

select * from tmp_categories;


select t.category, t.parent from tmp_categories t left join categories c on t.parent = c.code
where c.code is null;

insert into categories (
    code,
    parent
)
select t.category, t.parent from tmp_categories t join categories c on t.parent = c.code
on duplicate key update parent=t.parent;
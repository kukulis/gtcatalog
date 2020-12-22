update products p join classificators c on p.type = c.code
set p.code_from_custom = c.customs_code
where c.customs_code is not null and ( p.code_from_custom is null or p.code_from_custom = '' ) ;

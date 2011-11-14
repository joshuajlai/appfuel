-- give me all the descendant for a category
select		m.*
from		metadata as m
inner join	metadata_closure mc on m.meta_id = mc.descendant
where		mc.ancestor = 2
and			m.client_id = 1;

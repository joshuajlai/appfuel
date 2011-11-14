select		m.*
from		metadata as m
inner join	metadata_closure mc on m.meta_id = mc.descendant
where		mc.ancestor = 4
and			m.client_id = 1;

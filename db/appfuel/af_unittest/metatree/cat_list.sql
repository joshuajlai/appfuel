-- give me all categories for a client
select		m.meta_text, mc.asset_count, count(mc.descendant) as total_children
from		metadata_closure mc
inner join	metadata as m on mc.ancestor = m.meta_id
where		mc.depth = 1
and			m.client_id = 1
and			m.node_type = 'category'
group by	mc.ancestor;

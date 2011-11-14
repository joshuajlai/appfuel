-- I want a list of tags and thier categories for a given asset

select		m2.meta_text as cat, m.meta_text as tag

from		metadata m

inner join	asset_tag_assign as a on a.node_id = 2
inner join  metadata_closure as mc on mc.descendant = a.tag_id
and			mc.ancestor = a.cat_id
and			m.meta_id = a.tag_id

inner join metadata as m2 on m2.meta_id = mc.ancestor

where		m.client_id = 1;

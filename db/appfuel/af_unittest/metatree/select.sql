select		c.*
from		comments as c
inner join	comment_closure cc on c.comment_id = cc.descendant
where		cc.ancestor = 4

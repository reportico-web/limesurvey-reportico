


$survey = $this->get_criteria_value("survey");
$group = $this->get_criteria_value("group");

$swhere = "";
$gwhere = "";
if ( $survey ) $swhere = " AND s.sid in ( $survey )";
if ( $group ) $gwhere = " AND g.gid in ( $group )";
$sql = "create temporary t_questions as select s.sid, g.gid, q.qid, 
        g.group_name, 
        q.title,
        q.question,
        q.type,
        cq.title ch_title,
        CASE 
            WHEN cq.title IS NOT NULL THEN concat(s.sid, 'X', g.gid, 'X', q.qid, cq.title ) 
            ELSE concat(s.sid, 'X', g.gid, 'X', q.qid ) 
        END id
    from lime_surveys s
    join lime_groups g ON g.sid = s.sid $swhere $gwhere
    join lime_questions q ON q.gid = g.gid AND q.parent_qid  = 0
    left join lime_questions cq ON cq.parent_qid = q.qid
    where 1 = 1
";
//echo $sql;

$stmt = $_pdo->query($sql);


 

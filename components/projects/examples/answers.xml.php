

$survey = $this->get_criteria_value("survey");
$group = $this->get_criteria_value("group");

$swhere = "";
$gwhere = "";
if ( $survey ) $swhere = " AND s.sid in ( $survey )";
if ( $group ) $gwhere = " AND g.gid in ( $group )";
$sql = "select s.sid, g.gid, q.qid, 
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
//echo "<PRE>";
$the_unions = [];
while ( $row = $stmt->fetch() )
{
  $the_unions[]  = sprintf("SELECT '%s' group_name, \n
                                  '%s' title, \n
                                  '%s' question, \n
                                  '%s' type, \n
                                  '%s' id, \n
                                  '%s' qid, \n
                                  '%s' gid, \n
                                  '%s' ch_title, \n
                                  s.%s code, \n
                                    0 count
                            FROM lime_survey_%s AS s \n
                            LEFT JOIN lime_answers AS a \n
                              ON a.code = s.%s 
                              AND a.qid = '%s' \n"
      , $row["group_name"]
      , $row["title"]
      , preg_replace("/'/", "/\'/", $row["question"])
      , $row["type"]
      , $row["id"]
      , $row["qid"]
      , $row["gid"]
      , $row["ch_title"]
      , $row["id"]
      , $row["sid"]
      , $row["id"]
      , $row["qid"]);
}
 
$the_union = implode( "\nUNION ALL\n", $the_unions);
 
$the_union = "CREATE TEMPORARY TABLE t_answers AS $the_union";
$stmt = $_pdo->query($the_union);

//echo "<PRE>";
//echo $the_union; 
//echo "</PRE>";

$sql = "UPDATE t_answers SET count = 1 WHERE code IS NOT NULL";
$stmt = $_pdo->query($sql);


 

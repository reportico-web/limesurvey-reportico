survey=$1
mysql -v iconnex <<!
select c.column_name
        , RIGHT(g.group_name, 5) codice_reato
        , q.Title codice_domanda
    from information_schema.columns c
    left join lime_groups g
        ON LOCATE(CONCAT('X', g.gid, 'X') , c.column_name) = 7
    left join lime_questions q
        ON g.gid = q.gid
        and LOCATE(CONCAT('X', q.qid, 'E'), CONCAT(c.column_name, 'E')) > 8
    where c.table_name = CONCAT('lime_survey_', CAST('$survey' AS CHAR(8)))   #869765'
        #and left(c.column_name, 7) = CONCAT(CAST('$survey' AS CHAR(8)), 'X'); #'869765X';
!

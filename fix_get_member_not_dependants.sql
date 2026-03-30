-- Fix collation issue in get_member_not_dependants stored procedure
-- Run this SQL to fix the collation mismatch error

USE ims;

DROP PROCEDURE IF EXISTS get_member_not_dependants;

DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_member_not_dependants`(memberid varchar(15))
BEGIN

SELECT 
    d.id                                    as 'id',
    d.memberid                              as 'memberid',
    d.titleid                               as 'dependanttitleid',
    dt.description                          as 'dependanttitle',
    concat(ifnull(concat(dt.Description,' '),''),d.dependantname) as 'dependantfullname',
    d.dependantname                         as 'dependantname',
    d.relation                              as 'relation',
    d.dob                                   as 'dob',
    d.ismarried                             as 'ismarried',
    d.weddinganniversary                    as 'weddinganniversary',
    ds.id                                   as 'dependantspouseid',
    ds.dependantid                          as 'spousedependantid',
    ds.titleid                              as 'spousetitleid',
    st.description                          as 'spousetitle',
    concat(ifnull(concat(st.Description,' '),''),' ',ds.dependantname) as 'spousefullname',
    ds.dependantname                        as 'spousename',
    ds.dob                                  as 'spousedob',
    d.image                                 as 'dependantimage',
    ds.image                                as 'dependantspouseimage',
    d.thumbnailimage                        as 'dependantthumbnailimage',
    ds.thumbnailimage                       as 'dependantspousethumbnailimage'
FROM 
    dependant d
    LEFT JOIN dependant ds ON ds.dependantid = d.id AND d.dependantid IS NULL
    LEFT JOIN title dt ON d.titleid = dt.titleid
    LEFT JOIN title st ON ds.titleid = st.titleid
WHERE 
    d.tempmemberid COLLATE utf8mb3_general_ci = memberid COLLATE utf8mb3_general_ci 
    AND d.dependantid IS NULL
GROUP BY d.id
ORDER BY d.dependantname;

END$$

DELIMITER ;

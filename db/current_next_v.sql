CREATE VIEW current_next_v AS
SELECT  MN.name
       ,MN.url
       ,MN.status
       ,UNI.title
       ,UNI.release_date
FROM   (
       -- 次号
       SELECT   TAR.magazine_id
               ,TAR.title
               ,TAR.release_date
       FROM     titles_and_release_date TAR
                INNER JOIN (SELECT   magazine_id
                                    ,MIN(release_date) AS min_date
                            FROM     titles_and_release_date
                            WHERE    release_date >= current_date
                            GROUP BY magazine_id
                          ) AS MIN_TABLE
                      ON  TAR.magazine_id  = MIN_TABLE.magazine_id
                      AND TAR.release_date = MIN_TABLE.min_date
       UNION ALL
       -- 今号
       SELECT  TAR.magazine_id
              ,TAR.title
              ,TAR.release_date
       FROM   titles_and_release_date TAR
              INNER JOIN (SELECT   magazine_id
                                  ,MAX(release_date) AS max_date
                          FROM     titles_and_release_date
                          WHERE    release_date <= current_date
                          GROUP BY magazine_id
                         ) AS MAX_TABLE
                    ON  TAR.magazine_id  = MAX_TABLE.magazine_id
                    AND TAR.release_date = MAX_TABLE.max_date
       ) AS UNI
       INNER JOIN magazines MN
             ON   UNI.magazine_id = MN.id
ORDER BY  MN.name
        ,UNI.release_date DESC
;

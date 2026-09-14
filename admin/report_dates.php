<?php

/** Match the stored date format before parsing; day-first dates are not ISO dates. */
function report_year_expression(): string
{
    return "CASE
        WHEN TRIM(pb_date) REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}($| )'
            THEN YEAR(STR_TO_DATE(TRIM(pb_date), '%Y-%m-%d'))
        WHEN TRIM(pb_date) REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$'
            THEN YEAR(STR_TO_DATE(TRIM(pb_date), '%d-%m-%Y'))
        WHEN TRIM(pb_date) REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'
            THEN YEAR(STR_TO_DATE(TRIM(pb_date), '%d/%m/%Y'))
        ELSE NULL
    END";
}

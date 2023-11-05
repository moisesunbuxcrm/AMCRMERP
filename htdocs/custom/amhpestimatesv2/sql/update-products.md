# Update products from Excel

1. First get current list of products and details using the stored procedure called get_product_details_csv
2. Share the result as an Excel file with A^M to get updates to products
3. With the Excel file containing the changes, create a new sheet and put the following formulas in the new sheet

   a. In row 1, just copy the column names from the first sheet
   b. In the second row
; To update the products take the following steps
; 1. Run the SP 

```
- CALL update_product_details(22,'Horizontal Rolling Windows 200 L.M.I','37X63HRPGTWG5_16C','...',2000,NULL,NULL,NULL,NULL,NULL,NULL,L,NULL,NULL'WINDOW','Impact Product',NULL,NULL,NULL,NULL,'YES',NULL,NULL,NULL,NULL,NULL);
- CALL update_product_details(24,'Horizontal Rolling Windows 200 L.M.I','36.75X73.75HRXOPGTWG5.16C','',0,NULL,NULL,NULL,NULL,NULL,NULL,L,NULL,NULL'WINDOW','Impact Product','XO','PGT WINDOWS','WHITE','Horizontal Rolling Series','YES','GRAY','Insulated','GRAY','CLEAR','NONE');
- CALL update_product_details(25,'ECO 19 1/8 X 25 3/4 WHITE/GREY WHITE INTERLAYER','19.125X25.75SHELECOWG5.16W','',0,150,281,19.125,'19 1/8',25.75,'25 3/4', 25.75,'25 3/4','WINDOW','Impact Product','EQUAL LITES','ECO WINDOWS','WHITE','Single Hung Series','YES','WHITE','Insulated','GRAY','WHITE','NONE');
- CALL update_product_details(26,'ECO 19 1/8 X 38 1/8 SINGLE HUNG WHITE/GREY WHITE INTERLAYER','19.125X38.125SHELECOWG5.16W','',0,150,305,19.125,'19 1/8',38.125,'38 1/8',,25.75,'25 3/4''WINDOW','Impact Product','EQUAL LITES','ECO WINDOWS','WHITE','Single Hung Series','YES','WHITE','Insulated','GRAY','WHITE','NONE');
- CALL update_product_details(27,'ECO 19 1/8 X 50 3/8 WHITE/GREY WHITE INTERLAYER','19.125X50.375SHELECOWG5.16W','',0,150,357,19.125,'19 1/8',50.375,'50 3/8',25.75,'25 3/4','WINDOW','Impact Product','EQUAL LITES','ECO WINDOWS','WHITE','Single Hung Series','YES','WHITE','Insulated','GRAY','WHITE','NONE');
...
```

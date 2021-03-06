--TEST--
Test output bit boolean parameters and casts to boolean types.
--DESCRIPTION--
This test verifies that output bit boolean parameters are read and set correctly and output
1 or 0 as appropriate. The expected outputs consist of a true value as a bit,
false as a bit, a true value cast to a bit, a true value as an int.
--ENV--
PHPT_EXEC=true
--SKIPIF--
<?php require('skipif.inc'); ?>
--FILE--
<?php
/* Fails on PHP 7, producing 1's and 2's instead of 0's and 1's. */

require_once('MsCommon.inc');

$conn = Connect();
if (!$conn) {
    fatalError("Could not connect");
}

$stmt = sqlsrv_query($conn, "IF OBJECT_ID('testBoolean', 'P') IS NOT NULL DROP PROCEDURE testBoolean");

$createSP = <<<SQL
CREATE PROCEDURE testBoolean
@bit_true bit OUTPUT, @bit_false bit OUTPUT, @bit_cast_true bit OUTPUT, @int_true bit OUTPUT
AS
BEGIN
SET @bit_true = 1;
SET @bit_false = 0;
SET @bit_cast_true = CAST(1 AS bit);
SET @int_true = 1;
END
SQL;

$stmt = sqlsrv_query($conn, $createSP);

sqlsrv_free_stmt($stmt);

$callSP = "{call testBoolean(?, ?, ?, ?)}";

$bit_true = 0;
$bit_false = 0;
$bit_cast_true = 0;
$int_true = 0;
$params = array(array(&$bit_true, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT), array(&$bit_false, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT), array(&$bit_cast_true, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT), array(&$int_true, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_INT));

$stmt = sqlsrv_query($conn, $callSP, $params);

var_dump($bit_true);
var_dump($bit_false);
var_dump($bit_cast_true);
var_dump($int_true);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
--EXPECT--
int(1)
int(0)
int(1)
int(1)

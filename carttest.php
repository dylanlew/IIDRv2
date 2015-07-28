<h1>Cart tester</h1>

<form action="cart.php" method="post">
	<p><label>ID:</label> <input type="text" size="2" name="add" /></p>
    <p><label>Type:</label> <select name="type"><option value="course">Course</option><option value="store">Store</option></select></p>
    <p><label>QTY:</label> <input type="text" size="2" value="1" name="qty" /></p>
    <p><input type="submit" name="submit" value="Go" />
</form>
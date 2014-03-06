<?php

$request = $collector->getController();
$request['route'] = $collector->getRoute();
$request['status'] = $collector->getStatusCode();

if (isset($request['class'])) {
	$request['link'] = getFileLink($request['file'], $request['line']);
}

?>

<a class="pf-parent" title="Request">
    <div class="pf-icon pf-icon-request"></div>
	<span class="pf-badge"><?php echo $request['status'] ?></span>
	<?php echo $request['route'] ? $request['route'] : '-' ?>
</a>

<?php if ($request['class']) : ?>
<div class="pf-dropdown">

    <table class="pf-table pf-table-dropdown">
        <tbody>
            <tr>
                <td>Class</td>
                <?php if ($request['link']) : ?>
                <td><a href="<?php echo $request['link'] ?>"><?php echo $request['class'] ?></a></td>
				<?php else: ?>
                <td><?php echo $request['class'] ?></td>
				<?php endif ?>
            </tr>
            <tr>
                <td>Method</td>
                <td><?php echo $request['method'] ?></td>
            </tr>
        </tbody>
    </table>

</div>
<?php endif ?>
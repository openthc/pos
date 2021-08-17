
<div style="position:relative;">
<form action="/b2b/sync" autocomplete="off" method="post">
    <div class="btn-group" style="position:absolute; right: 0.25em; top: 0.25em;">
        <a class="btn btn-outline-primary" href="/inventory/create"><i class="fas fa-plus"></i> Create</a>
        <button class="btn btn-outline-secondary"><i class="fas fa-sync"></i></button>
    </div>
</form>
</div>

<h1>Inventory Management</h1>

<div>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?= ($data['view_mode'] == "100" ? "active" : '') ?>" href="?view=100">Unpriced</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($data['view_mode'] == "flower" ? "active" : '') ?>" href="?view=flower">Flower</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($data['view_mode'] == "extract" ? "active" : '') ?>" href="?view=extract">Extract</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($data['view_mode'] == "edible" ? "active" : '') ?>" href="?view=edible">Edibles</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($data['view_mode'] == "samples" ? "active" : '') ?>" href="/inventory/samples">Samples</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($data['view_mode'] == "misc" ? "active" : '') ?>" href="?view=misc">Other Stuff</a>
        </li>
    </ul>
</div>

<div id="inventory-list"></div>


<script>
$(function() {
    $('#inventory-list').html('<i class="fas fa-sync fa-spin"></i> Loading...');
    $('#inventory-list').load('/inventory/ajax', {
        view: '<?= $data['view_mode'] ?>'
    });
});
</script>

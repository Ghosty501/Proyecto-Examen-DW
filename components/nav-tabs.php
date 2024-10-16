<div class="mt-3 tabs">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="resultados-tab" data-bs-toggle="tab" data-bs-target="Resultados.php" type="button" role="tab">Resultados</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="categoria-tab" data-bs-toggle="tab" data-bs-target="Categoria.php" type="button" role="tab">Categorías</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="resultados-tab-pane" role="tabpanel" tabindex="0"><?php include 'Resultados.php'?></div>
        <div class="tab-pane fade" id="categoria-tab-pane" role="tabpanel" tabindex="0"><?php include 'Categoria.php'?></div>
    </div>
</div>
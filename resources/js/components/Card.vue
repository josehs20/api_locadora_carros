<template>
    <!--Inicio do card de busca-->
    <div class="card mb-3">
        <div class="card-header">.:{{ titulo }}</div>
        <div class="card-body">
            <div class="row">

                <slot name="conteudo"></slot>

            </div>
        </div>
        <div class="card-footer">
            <slot name="rodape"></slot>
        </div>
    </div>
    <!--Fim do card de busca-->
</template>

<script>
export default {
    props: ['titulo']
}
</script>

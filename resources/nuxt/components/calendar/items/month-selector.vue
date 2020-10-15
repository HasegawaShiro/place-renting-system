<template>
    <dialog
        class="ts mini closable modal month selector"
        :class="fullOnParent ? fullOnParentClass : ''"
    >
        <div class="content">
            內容內容內容內容內容內容內容內容內容內容內容內容內容內容 內容內容內容內容內容內容內容內容內容內容內容內容內容內容
        </div>
    </dialog>
</template>

<script>
export default {
    data() {
        return {
            fullOnParentClass: ['full', 'on', 'parent'],
        };
    },
    props: {
        'full-on-parent': {
            type: Boolean,
            default() {
                return false;
            },
        },
        'opened-date': {
            type: Date,
            default() {
                return new Date();
            }
        },
        'modal-open': {
            type: Boolean,
            default() {
                return false;
            }
        },
    },
    computed: {
    },
    watch: {
        modalOpen: function(newVal, oldVal) {
            console.log(newVal);
            if(newVal){
                ts('.month.selector').modal('show');
            }else{
                ts('.month.selector').modal('hide');
            }
        }
    }
}
</script>

<style>
.month.selector {
    position: fixed;
    z-index: 5;
    left: 5em;
}

</style>

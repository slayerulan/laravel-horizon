<template>
<li class="dropdown">
    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
        <i class="material-icons">notifications</i>
        <span class="badge">{{ notifications.length }}</span>
    </a>
    <ul class="dropdown-menu">
        <li v-if="notifications.length == 0>
        No Ticket
        </li>
    </ul>
</li>
</template>

<script>
    export default {
        props: ['notifications']
    }
</script>

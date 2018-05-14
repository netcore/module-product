<template>
    <ol class="breadcrumb page-breadcrumb">
        <li><a href="/">Homepage</a></li>
        <li><a href="/admin">Dashboard</a></li>

        <li v-for="(data, name) in breadcrumb" :class="{active: $route.name === name}">
            <router-link :to="{name, params: getParams(data)}" v-text="getTitle(data)"></router-link>
        </li>
    </ol>
</template>

<script>
    export default {
    	props: {
    		breadcrumb: {
                type: Object,
                default() {
                	return {};
                }
            }
        },

        methods: {
    		getParams(data) {
    			if(typeof data === 'object' && data.params) {
    				return data.params;
                }

                return {};
            },

            getTitle(data) {
				if(typeof data === 'object' && data.title) {
					return data.title;
				}

				return data;
            }
        }
    }
</script>
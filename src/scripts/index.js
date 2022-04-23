import { createApp } from 'vue';

import DemoComponent from './component/demoComponent';

createApp({
    components: {
        'demo-component': DemoComponent,
    },
    mixins: [],
    data() {
        return {
            lang: 'en-US',
        }
    },
    mounted() {},
    computed: {},
    methods: {},
}).mount('#vue-app');

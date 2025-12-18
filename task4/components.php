<html>
<body>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<div id="app" >
    <ul>
        <li v-for="v in people" >{{ v['name'] }}</li>
    </ul>
    <pre>{{ people }}</pre>
    <renderer v-bind:data="people"></renderer>
</div>
<script>
var renderer = {
    data: function(){
        return {
            "a": "abcd"
        };
    },
    props:[ "data"],
    template: `<div>
        <ul>
            <li v-for="v in data" >
                <div>{{ v['name'] }}</div>
                <renderer v-if="'team' in v" v-bind:data="v['team']" ></renderer>
            </li>
        </ul>
    </div>`
};
var app = Vue.createApp({
    data:function(){
        return { 
            "people": [
                {
                    "name": "Satish",
                    "team": [
                        {
                            "name": "sagar",
                            "team": [
                                {
                                    "name": "apparao",
                                },
                                {
                                    "name": "subbarao"
                                }
                            ]
                        }
                    ]
                }
            ]
        };
    },

});
app.component("renderer", renderer);
app.mount("#app");
</script>
</body>
</html>
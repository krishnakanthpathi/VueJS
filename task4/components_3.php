<html>
<body>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>


</script>
<div id="app" >
    <renderer v-bind:data="data"></renderer>
</div>
<script>
var renderer = {
    data: function(){
        return {
            "new_item_type": "string",
            "new_item_key": "",
        };
    },
    methods: {
        add_list_item: function(){
            if( this.new_item_type == "string" ){
                this.data.push("New String");
            }else if( this.new_item_type == "object" ){
                this.data.push({"a":"b"});
            }else if( this.new_item_type == "list" ){
                this.data.push(["string"]);
            }
        },
        add_object_item: function(){
            if( this.new_item_type == "string" ){
                this.data[ this.new_item_key ] = "New String";
            }else if( this.new_item_type == "object" ){
                this.data[ this.new_item_key ] = {"a":"b"};
            }else if( this.new_item_type == "list" ){
                this.data[ this.new_item_key ] = ["string"];
            }
        }
    },
    props:[ "data"],
    template: `<div>
        <ol v-if="typeof(data)=='object'&&'length' in data">
            <li v-for="v,i in data" >
                <div style="display:flex; column-gap:20px;" >
                    <input type="button" value="X" v-on:click="delete_list_item()">    
                    <renderer v-if="typeof(v)=='object'" v-bind:data="v" ></renderer>
                    <div v-else>{{ v }}</div>
                </div>
            </li>
            <li>
                <div style="display:flex; column-gap:20px;" >
                    <select v-model="new_item_type" >
                        <option value="string" >string</option>
                        <option value="object" >object</option>
                        <option value="list" >list</option>
                    </select>
                    <input type="button" value="+" v-on:click="add_list_item()">
                </div>
            </li>
        </ol>
        <ul v-else-if="typeof(data)=='object'">
            <li v-for="v,k in data" >
                <div style="display:flex; column-gap:20px;" >
                    <div>{{ k }}: <span v-if="typeof(v)!='object'">{{ v }}</span></div>
                    <renderer v-if="typeof(v)=='object'&&v!=null" v-bind:data="v" ></renderer>
                </div>
            </li>
            <li>
                <div style="display:flex; column-gap:20px;" >
                    <input type="text" placeholder="Key" v-model="new_item_key" >
                    <select v-model="new_item_type" >
                        <option value="string" >string</option>
                        <option value="object" >object</option>
                        <option value="list" >list</option>
                    </select>
                    <input type="button" value="+" v-on:click="add_object_item()">
                </div>
            </li>
        </ul>
    </div>`
};
var app = Vue.createApp({
    data:function(){
        return { 
            "data": ["satish", "raju"]
        };
    },

});
app.component("renderer", renderer);
app.mount("#app");
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursive Component</title>
    <!-- vue js cdn -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- tailwind css cdn -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> -->

</head>
<body>
    <div id="app" class="p-4">
            <h1>Recursive Component</h1>
            <recursive :node="treeData"></recursive>
            
    </div>
    
</body>

<script>
    const app = Vue.createApp({
        data(){
            return {
                // type
                test:["krishna" , "mohan" , "sai" ,],
                // test:["krishna" , "mohan" , "sai" , {"pair1" : "value1"} ,],
                treeData: [
                {
                    "projectName": "Vue Recursive Editor",
                    "version": "1.0.4",
                    "isPublic": true,
                    "settings": {
                        "theme": "dark",
                        "autoSave": true, 
                        "maxRetries": 5
                    },
                    "metadata": null,
                    "contributors": [
                        "Alice",
                        "Bob",
                        "Charlie"
                    ],
                    "logs": {
                        "lastEdited": "2024-06-15T10:20:30Z",
                        "editCount": 42,
                        "editHistory": [
                            {"editor": "Alice", "timestamp": "2024-06-14T09:15:00Z"},
                            {"editor": "Bob", "timestamp": "2024-06-13T14:22:10Z"}
                        ]
                    },
                    "features": [
                        {
                            "id": 1, 
                            "name": "Live Edit", 
                            "status": "active"
                        },
                        {
                            "id": 2, 
                            "name": "Export PDF", 
                            "status": "beta",
                            "flags": ["experimental", "paid-only"]
                        }
                    ]
            },
            {
                "simpleList": [1, 2, 3, 4, 5] ,
                "emptyObject": {}
            }
            ]
        }},

    });

    app.component('recursive' ,{
        props: ['node'],
        data(){
            return {
                type1:"string",
                type2:"list",
                keyValue:"",
                objkey:"",
                objval:""
            }
        },
        methods: {
            add_item : function (){
                if( this.type1 == "string" ){
                    this.node.push(this.keyValue);
                }else if( this.type1 == "object" ){
                    this.node.push( {} );
                }else if( this.type1 == "list" ){
                    this.node.push( [] );
                }
            }  ,
            key_change : function (){
                if( this.type2 == "string" ){
                    this.node[this.objkey] = this.objval;
                }else if( this.type2 == "object" ){
                    this.node[this.objkey] = {};
                }else if( this.type2 == "list" ){
                    this.node[this.objkey] = [];
                }
            }
        },
        template: `
            <div v-if="Array.isArray(node)">
                <ol  >
                    <li  v-for="obj in node" ><recursive :node="obj"></recursive></li>
                    
                    <li >

                        <select v-model="type1" >
                                <option value="string" >string</option>
                                <option value="object" >object</option>
                                <option value="list" >list</option>
                        </select>
                        <input v-if="type1 == 'string'" type="text" v-model="keyValue" placeholder="value" >
                        <input type="button" value="+" v-on:click="add_item()" >
                    </li>
                </ol>
            </div>
            <div v-else-if="typeof node === 'object' && node !== null">
                <ul   >
                    <li v-for="(value, key) in node" > {{key}} : <recursive :node="node[key]"></recursive></li>

                    <li >
                        
                        <input  v-model="objkey" type="text" placeholder="key name" >
                        <input v-if="type2 == 'string'"  v-model="objval" type="text" placeholder="value" >
                        <select v-model="type2" >
                                <option value="object" >object</option>
                                <option value="string" >string</option>
                                <option value="list" >list</option>
                        </select>
                        <input type="button" value="+" v-on:click="key_change()" >
                    </li>
                </ul>
            </div>
            <span v-else>
                 {{node}}
            </span>  
            
        `

    })
    app.mount('#app')


</script>


</html>


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
                test:["krishna" , "mohan" , "sai" , {"pair1" : "value1"} ,],
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
        mounted(){
            this.dfs( this.treeData , "" );
        },
        methods: {
            dfs : function(node , pad){
                if( Array.isArray(node) ){
                    for( let item of node ){
                        this.dfs(item , pad + "  " );
                    }
                }else if( typeof node === 'object' && node !== null ){
                    for( let key in node ){
                        console.log(pad + key + " : " );
                        this.dfs( node[key] , pad + "  " );
                    }
                }else{
                    console.log( pad + node );
                }
            }
        }
    });

    app.component('recursive' ,{
        props: ['node'],
        template: `
            <div v-if="Array.isArray(node)">
                <ul v-for="obj in node"  >
                    <li ><recursive :node="obj"></recursive></li>
                </ul>
            </div>
            <div v-else-if="typeof node === 'object' && node !== null">
                <ul v-for="(value, key) in node"  >
                    <li > {{key}} : <recursive :node="node[key]"></recursive></li>
                </ul>
            </div>
            <span v-else >
                 {{node}}
            </span>  
                
        `

    })
    app.mount('#app')


</script>


</html>


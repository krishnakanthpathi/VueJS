res = "";
function dfs(node , pad){
    if( Array.isArray(node) ){
        res += pad + "\n"
        for( let item of node ){
            res += pad ;
            dfs(item , pad + "  " );
        }
    }else if( typeof node === 'object' && node !== null ){
        res += "\n"
        for( let key in node ){
            res += pad + "-" + key + " : " 
            dfs( node[key] , pad + "  " );
        }
    }else{
        res +=String(node) + "\n"
    }
}

// Example usage:
const data = [
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
];

dfs( data , "" );

// write to text file
const fs = require('fs');
fs.writeFileSync( 'output.txt' , res );
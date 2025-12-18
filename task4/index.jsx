// jsx
const recursive_component = function(node) {
    return (
        <div class="ml-4 mt-2">
            <div>{node.name}</div>
            {node.children && node.children.length ? (
                node.children.map((child, index) => (
                    <recursive_component key={index} node={child} />
                ))
            ) : null}
        </div>
    );
}
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    private static $clients = [];
    private static $nextId = 1;

    // Listar todos os clientes
    public function index()
    {
        return response()->json(self::$clients);
    }

    // Criar um novo cliente
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $client = [
            'id' => self::$nextId++,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        self::$clients[] = $client;

        return response()->json($client, 201);
    }

    // Exibir um cliente específico
    public function show($id)
    {
        $client = collect(self::$clients)->firstWhere('id', $id);

        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        return response()->json($client);
    }

    // Atualizar um cliente
    public function update(Request $request, $id)
    {
        $index = collect(self::$clients)->search(fn ($c) => $c['id'] == $id);

        if ($index === false) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        self::$clients[$index] = array_merge(self::$clients[$index], $validated);

        return response()->json(self::$clients[$index]);
    }

    // Excluir um cliente
    public function destroy($id)
    {
        $index = collect(self::$clients)->search(fn ($c) => $c['id'] == $id);

        if ($index === false) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        array_splice(self::$clients, $index, 1);

        return response()->json(['message' => 'Client deleted successfully']);
    }
}

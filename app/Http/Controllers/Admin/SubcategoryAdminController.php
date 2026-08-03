<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryAdminController extends Controller
{
    public function store(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);
        $data['is_active'] = true;
        // Se agrega siempre al final del listado; el orden se ajusta luego con las flechas.
        $data['order'] = ((int) $category->subcategories()->max('order')) + 1;
        $category->subcategories()->create($data);

        return back()->with('success', 'Subcategoría creada.');
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $subcategory->update($data);

        return back()->with('success', 'Subcategoría actualizada.');
    }

    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();
        return back()->with('success', 'Subcategoría eliminada.');
    }

    public function moveUp(Subcategory $subcategory)
    {
        $this->swapWithNeighbor($subcategory, -1);
        return back()->with('success', 'Orden actualizado.');
    }

    public function moveDown(Subcategory $subcategory)
    {
        $this->swapWithNeighbor($subcategory, 1);
        return back()->with('success', 'Orden actualizado.');
    }

    /**
     * Reordena moviendo la subcategoría una posición hacia arriba (-1) o abajo (+1)
     * dentro de su categoría, y de paso renumera todo el grupo de forma secuencial
     * (0..n-1) para que valores de `order` repetidos o salteados de antes queden
     * corregidos automáticamente.
     */
    private function swapWithNeighbor(Subcategory $subcategory, int $offset): void
    {
        $siblingIds = Subcategory::where('category_id', $subcategory->category_id)
            ->ordered()
            ->pluck('id')
            ->all();

        $currentIndex = array_search($subcategory->id, $siblingIds, true);
        $targetIndex = $currentIndex + $offset;

        if ($currentIndex === false || $targetIndex < 0 || $targetIndex >= count($siblingIds)) {
            return;
        }

        [$siblingIds[$currentIndex], $siblingIds[$targetIndex]] = [$siblingIds[$targetIndex], $siblingIds[$currentIndex]];

        foreach ($siblingIds as $position => $id) {
            Subcategory::where('id', $id)->update(['order' => $position]);
        }
    }
}

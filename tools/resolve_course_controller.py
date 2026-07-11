#!/usr/bin/env python3
"""Merge CourseController from both branches."""
import subprocess

root = r"E:\DATN"
path = "app/Http/Controllers/Web/CourseController.php"
ours = subprocess.check_output(["git", "-C", root, "show", f":2:{path}"], text=True)
theirs = subprocess.check_output(["git", "-C", root, "show", f":3:{path}"], text=True)

# Start from TuanTu (theirs) as base for LearningPlayerService integration
content = theirs

# Add rating filter from ours if missing pieces
if "use App\\Services\\LearningPlayerService;" not in content:
    content = content.replace(
        "use App\\Models\\Review;",
        "use App\\Models\\Review;\nuse App\\Services\\LearningPlayerService;",
    )

# Enhanced search from HEAD
content = content.replace(
    "                $query->where('title', 'like', \"%{$search}%\");",
    """                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhereHas('instructor', function ($qInst) use ($search) {
                          $qInst->where('name', 'like', "%{$search}%");
                      });
                });""",
)

# Add rating variable and filters if not present
if "$rating = $request->query('rating');" not in content:
    content = content.replace(
        "        $pricing = $request->query('pricing');",
        "        $pricing = $request->query('pricing');\n        $rating = $request->query('rating');",
    )

if "->when($rating," not in content:
    content = content.replace(
        "            ->when($pricing === 'paid', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 0'))",
        """            ->when($pricing === 'under_200k', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 0 AND COALESCE(discount_price, sale_price, price) <= 200000'))
            ->when($pricing === '200k_500k', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) >= 200000 AND COALESCE(discount_price, sale_price, price) <= 500000'))
            ->when($pricing === 'above_500k', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 500000'))
            ->when($pricing === 'paid', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 0'))
            ->when($rating, fn ($query) => $query->where('rating_avg', '>=', (float) $rating))""",
    )

if "'rating'" not in content.split("compact(")[1].split(");")[0]:
    content = content.replace(
        "            'pricing'\n        ));",
        "            'pricing',\n            'rating'\n        ));",
    )

# Preview lesson filter in show when not full access
if "when(!$canAccessFullCourse" not in content:
    content = content.replace(
        "                ->orderBy('sort_order'),\n            'chapters.lessons'",
        "                ->when(! $canAccessFullCourse, fn ($query) => $query->where('is_preview', true))\n                ->orderBy('sort_order'),\n            'chapters.lessons'",
        1,
    )
    content = content.replace(
        "'chapters.lessons' => fn ($q) => $q\n                ->select(",
        "'chapters.lessons' => fn ($q) => $q\n                ->select(",
    )

out = rf"{root}\{path.replace('/', '\\')}"
with open(out, "w", encoding="utf-8", newline="\n") as f:
    f.write(content)
print("Wrote", out)

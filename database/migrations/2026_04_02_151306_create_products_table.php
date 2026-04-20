use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // معلومات أساسية
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // الأسعار
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();

            // المخزون
            $table->integer('quantity')->default(0);
            $table->string('sku')->nullable()->unique();

            // العلاقات
            $table->foreignId('category_id')
                  ->constrained()
                  ->restrictOnDelete();

            // خصائص العرض
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_hidden')->default(false);

            $table->timestamps();

            // تحسين الأداء
            $table->index('category_id');
            $table->index('is_featured');
            $table->index('is_hidden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
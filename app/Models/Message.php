<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Message extends Model
{
    use HasFactory;
   protected $fillable = ['sender_id','recipient_id','body'];
   public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// In User.php

// <?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     public function up(): void
//     {
//         Schema::create('messages', function (Blueprint $table) {
//             $table->id();
//             $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
//             $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
//             $table->text('body')->nullable();
//             $table->timestamp('read_at')->nullable();
//             $table->timestamps();
//         });
//     }

//     public function down(): void
//     {
//         Schema::dropIfExists('messages');
//     }
// };

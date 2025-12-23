<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;

class FixCategoryEncoding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:fix-encoding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sửa lỗi encoding cho categories bị double encoding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categories = Category::all();
        $fixed = 0;
        $skipped = 0;
        $needsReimport = [];

        foreach ($categories as $category) {
            $originalName = $category->name;
            
            // Kiểm tra xem có ký tự tiếng Việt hợp lệ không
            $hasValidVietnamese = preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐỒỒỐỘỔỖỚỜỞỠỢỰỬỮỲỴỶỸ]/u', $originalName);
            
            // Kiểm tra xem có ký tự lạ không (dấu hiệu của double encoding)
            // Các ký tự như Ä, á», Ã, € là dấu hiệu của double encoding
            $hasGarbledChars = preg_match('/[Äá»Ã€]/u', $originalName);
            
            if ($hasGarbledChars) {
                // Dữ liệu bị lỗi encoding, cần import lại
                $needsReimport[] = [
                    'id' => $category->id,
                    'name' => $originalName,
                    'description' => $category->description
                ];
                $this->warn("Category ID {$category->id} cần import lại: {$originalName}");
            } else {
                $skipped++;
            }
        }

        if (count($needsReimport) > 0) {
            $this->info("Có " . count($needsReimport) . " categories cần import lại.");
            $this->info("Vui lòng xóa các categories này và import lại từ file CSV với code mới.");
        } else {
            $this->info("Tất cả categories đều OK, không cần sửa.");
        }

        $this->info("Đã kiểm tra " . count($categories) . " categories.");
        return 0;
    }

    /**
     * Sửa encoding cho một string
     * Fix double encoding: UTF-8 được encode lại như thể nó là ISO-8859-1
     */
    private function fixEncoding($value)
    {
        if (!is_string($value) || empty($value)) {
            return $value;
        }

        // Nếu đã là UTF-8 hợp lệ, kiểm tra xem có phải double encoding không
        if (mb_check_encoding($value, 'UTF-8')) {
            // Thử decode từ UTF-8 về ISO-8859-1 (Latin-1) rồi encode lại
            // Đây là cách fix double encoding phổ biến
            $decoded = @mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
            if ($decoded !== false) {
                $reEncoded = mb_convert_encoding($decoded, 'UTF-8', 'ISO-8859-1');
                if ($reEncoded !== false && mb_check_encoding($reEncoded, 'UTF-8')) {
                    // Nếu sau khi decode và encode lại khác với giá trị gốc,
                    // và có vẻ hợp lý hơn (ít ký tự lạ hơn), thì dùng nó
                    if ($reEncoded !== $value) {
                        // Kiểm tra xem có ký tự tiếng Việt hợp lệ không
                        if (preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/u', $reEncoded)) {
                            return $reEncoded;
                        }
                    }
                }
            }
        }

        // Thử cách khác: nếu có vẻ như bị double encode từ Windows-1252
        $decoded = @mb_convert_encoding($value, 'Windows-1252', 'UTF-8');
        if ($decoded !== false) {
            $reEncoded = mb_convert_encoding($decoded, 'UTF-8', 'Windows-1252');
            if ($reEncoded !== false && mb_check_encoding($reEncoded, 'UTF-8')) {
                if ($reEncoded !== $value && preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/u', $reEncoded)) {
                    return $reEncoded;
                }
            }
        }

        // Nếu không fix được, trả về giá trị gốc
        return $value;
    }
}

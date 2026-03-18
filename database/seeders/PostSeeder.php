<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::query()->count() < 10) {
            User::factory()->count(10 - User::query()->count())->create();
        }

        $users = User::query()->pluck('id');
        $attachmentPool = $this->createAttachmentPool(12);

        Post::factory()
            ->count(30)
            ->state(fn (): array => [
                'user_id' => $users->random(),
                'attachment' => (! empty($attachmentPool) && random_int(1, 100) <= 45)
                    ? $attachmentPool[array_rand($attachmentPool)]
                    : null,
            ])
            ->create();
    }

    /**
     * @return array<int, string>
     */
    private function createAttachmentPool(int $count): array
    {
        $disk = Storage::disk('public');
        $directory = 'post-attachments';
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $paths = collect($disk->files($directory))
            ->filter(fn (string $path): bool => in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $allowedExtensions, true))
            ->values()
            ->all();

        if (! function_exists('imagecreatetruecolor')) {
            return $paths;
        }

        for ($i = count($paths); $i < $count; $i++) {
            $image = $this->generateImageBinary();
            $path = $directory . '/' . Str::uuid() . '.' . $image['extension'];
            $disk->put($path, $image['binary']);
            $paths[] = $path;
        }

        return $paths;
    }

    /**
     * @return array{extension: string, binary: string}
     */
    private function generateImageBinary(): array
    {
        $width = 1280;
        $height = 720;
        $image = imagecreatetruecolor($width, $height);

        $start = [random_int(20, 100), random_int(80, 180), random_int(160, 255)];
        $end = [random_int(120, 220), random_int(40, 120), random_int(80, 200)];

        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / max(1, $height - 1);
            $red = (int) round($start[0] + ($end[0] - $start[0]) * $ratio);
            $green = (int) round($start[1] + ($end[1] - $start[1]) * $ratio);
            $blue = (int) round($start[2] + ($end[2] - $start[2]) * $ratio);
            $lineColor = imagecolorallocate($image, $red, $green, $blue);
            imageline($image, 0, $y, $width, $y, $lineColor);
        }

        for ($i = 0; $i < 6; $i++) {
            $overlay = imagecolorallocatealpha(
                $image,
                random_int(200, 255),
                random_int(200, 255),
                random_int(200, 255),
                random_int(85, 110)
            );

            imagefilledellipse(
                $image,
                random_int(0, $width),
                random_int(0, $height),
                random_int(180, 520),
                random_int(120, 360),
                $overlay
            );
        }

        $captionColor = imagecolorallocate($image, 255, 255, 255);
        imagestring(
            $image,
            5,
            24,
            24,
            'Dummy post image #' . random_int(1000, 9999),
            $captionColor
        );

        ob_start();
        imagejpeg($image, null, 88);
        $binary = (string) ob_get_clean();
        imagedestroy($image);

        return [
            'extension' => 'jpg',
            'binary' => $binary,
        ];
    }
}

<?php
namespace kyselo;
class resizer
{
    public $ext;

    function resize($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $this->ext = $ext;
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $ok = exec('jpegoptim --all-progressive --size=2000 -- ' .escapeshellarg($path));
                break;
            case 'png':
                $ok = exec('pngquant --skip-if-larger --quality=65-80 --ext .png --force ' . escapeshellarg($path));
                break;
            case '3gp':
            case 'gif':
            case 'm2ts':
            case 'mov':
            case 'avi':
            case 'wmw':
                $newPath = str_replace('.' . $ext, '.mp4', $path);
                // from https://gist.github.com/dvlden/b9d923cb31775f92fa54eb8c39ccd5a9
                $ok = exec('ffmpeg -i '.escapeshellarg($path).' -preset slow -codec:a aac -b:a 128k -codec:v libx264 -pix_fmt yuv420p -b:v 1000k -minrate 500k -maxrate 2000k -bufsize 2000k -vf scale=-2:480 '.escapeshellarg($newPath));
                $this->ext = 'mp4';
                unlink($path);
                if (!file_exists($newPath)) {
                    return false;
                }
                break;
            case 'wav':
            case 'flac':
            case 'opus':
                $newPath = str_replace('.' . $ext, '.mp3', $path);
                // from https://stackoverflow.com/questions/3255674/convert-audio-files-to-mp3-using-ffmpeg
                $ok = exec('ffmpeg -i '.escapeshellarg($path).' -vn -ar 44100 -ac 2 -b:a 192k '.escapeshellarg($newPath));
                $this->ext = 'mp3';
                unlink($path);
                if (!file_exists($newPath)) {
                    return false;
                }
                break;
            case 'mp4':
            case 'mp3':
                // nothing to do here, these are already compressed
                $ok = true;
                $this->ext = $ext;
                break;
            default:
                $this->ext = false;
                return false;
        }
        return $ok!==false;

    }
}

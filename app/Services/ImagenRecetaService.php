<?php

namespace App\Services;

use App\Models\Receta;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Servicio modular para la gestión de imágenes de recetas.
 * Centraliza la subida, almacenamiento y eliminación de imágenes.
 */
class ImagenRecetaService
{
    /**
     * Directorio base donde se almacenan las imágenes de recetas (relativo a public/).
     */
    protected string $directorio = 'assets/img/recetas';

    /**
     * Sube la imagen principal de una receta.
     *
     * @param UploadedFile $file Archivo subido
     * @return string Ruta relativa almacenada (ej: assets/img/recetas/abc123.jpg)
     */
    public function subirImagenPrincipal(UploadedFile $file): string
    {
        return $this->guardarArchivo($file);
    }

    /**
     * Sube múltiples imágenes para los pasos de una receta.
     *
     * @param array $archivos Array indexado [indice_paso => UploadedFile|null]
     * @param array $rutasExistentes Rutas existentes a conservar si no se reemplazan
     * @return array Array indexado [indice_paso => ruta_imagen]
     */
    public function subirImagenesPasos(array $archivos, array $rutasExistentes = []): array
    {
        $resultado = $rutasExistentes;

        foreach ($archivos as $indice => $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                // Si había una imagen anterior en esta posición, eliminarla
                if (isset($rutasExistentes[$indice]) && $rutasExistentes[$indice]) {
                    $this->eliminarImagen($rutasExistentes[$indice]);
                }
                $resultado[$indice] = $this->guardarArchivo($file);
            }
        }

        return $resultado;
    }

    /**
     * Elimina un archivo de imagen del sistema de ficheros.
     *
     * @param string $ruta Ruta relativa (ej: assets/img/recetas/abc123.jpg)
     */
    public function eliminarImagen(string $ruta): void
    {
        // No borrar la imagen por defecto
        if ($ruta === 'assets/img/logo.png' || empty($ruta)) {
            return;
        }

        $rutaCompleta = public_path($ruta);

        if (File::exists($rutaCompleta)) {
            File::delete($rutaCompleta);
        }
    }

    /**
     * Elimina todas las imágenes asociadas a una receta (principal + pasos).
     *
     * @param Receta $receta La receta cuyas imágenes se eliminan
     */
    public function eliminarTodasImagenes(Receta $receta): void
    {
        // Eliminar imagen principal
        if ($receta->url_imagen) {
            $this->eliminarImagen($receta->url_imagen);
        }

        // Eliminar imágenes de los pasos
        $imagenesPasos = $receta->imagenes_pasos;
        if (is_array($imagenesPasos)) {
            foreach ($imagenesPasos as $rutaPaso) {
                if ($rutaPaso) {
                    $this->eliminarImagen($rutaPaso);
                }
            }
        }
    }

    /**
     * Guarda un archivo en el directorio de recetas con nombre único hasheado.
     *
     * @param UploadedFile $file Archivo a guardar
     * @return string Ruta relativa del archivo guardado
     */
    protected function guardarArchivo(UploadedFile $file): string
    {
        $this->asegurarDirectorio();

        $extension = $file->getClientOriginalExtension();
        $nombreUnico = Str::uuid() . '.' . $extension;

        $file->move(public_path($this->directorio), $nombreUnico);

        return $this->directorio . '/' . $nombreUnico;
    }

    /**
     * Asegura que el directorio de destino existe.
     */
    protected function asegurarDirectorio(): void
    {
        $ruta = public_path($this->directorio);

        if (!File::isDirectory($ruta)) {
            File::makeDirectory($ruta, 0755, true);
        }
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Service\Export;

use App\Service\File\File;
use App\Utils\Generator;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet;

final class ExportExcel
{
    private $spreadsheet;
    /* @var  Worksheet $sheet */
    private $sheet;
    private $title;
    private $sheetTitle;
    private $headers;
    private $items;
    private $start = 'A';
    private $row = 3;

    /**
     * @throws Exception
     */
    public function __construct(array $headers, array $items, string $title = 'export', string $sheetTitle = 'info')
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->init($headers, $items, $title, $sheetTitle);
    }

    /**
     * @throws Exception
     */
    public function init(array $headers, array $items, string $title = 'export', string $sheetTitle = 'info'): self
    {
        $this->headers = $headers;
        $this->items = $items;
        $this->title = $title;
        $this->sheetTitle = $sheetTitle;

        $this->process();

        return $this;
    }

    /**
     * @throws Exception
     */
    private function process(): self
    {
        // establecer las cabeceras
        $column = \ord($this->start) - 1;
        foreach ($this->headers as $key => $label) {
            ++$column;
            $this->sheet->setCellValue(\chr($column).$this->row, $label);
        }

        $i = $this->row + 1;
        foreach ($this->items as $item) {
            $column = \ord($this->start) - 1;
            foreach ($this->headers as $key => $label) {
                ++$column;
                $this->sheet->setCellValue(\chr($column).$i, $this->itemByKey($item, $key));
            }
            ++$i;
        }

        $this->headerSheet($this->start, \chr($column), $this->row);
        $this->dataStyle($this->start, \chr($column), $this->row);

        return $this;
    }

    private function itemByKey(array $item, string $key)
    {
        $indexes = explode('.', $key);

        return $this->item($item, $indexes, 0);
    }

    private function item($item, array $indexes, int $count)
    {
        $key = $indexes[$count];
        if (\is_array($item[$key])) {
            return $this->item($item[$key], $indexes, $count + 1);
        }

        return $item[$key];
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function fileDownload(): File
    {
        $writer = new Xlsx($this->spreadsheet);
        $fileName = Generator::slugify($this->sheetTitle()).'_'.(new \DateTime())->format('dmY_his').'.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return new File($fileName, $tempFile);
    }

    /**
     * @throws Exception
     */
    private function headerSheet(string $start, string $end, int $row): self
    {
        if (null !== $this->title) {
            if (null !== $this->sheetTitle) {
                $this->sheet->setTitle($this->sheetTitle);
            }
            $this->sheet->setCellValue($start.'1', $this->title);
            $this->sheet->mergeCells($start.'1:'.$end.'1');
            $this->sheet->mergeCells($start.'2:'.$end.'2');

            // estableciendo estilos de titulos
            $styleArrayTitulo = [
                'font' => [
                    'bold' => true,
                    'size' => '14',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];
            $this->sheet->getStyle($start.'1:'.$end.'2')
                ->applyFromArray($styleArrayTitulo);
            $this->sheet->getStyle($start.'1:'.$end.'2')
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    private function dataStyle(string $start, string $end, int $row): void
    {
        // estilos para las cabeceras
        $styleArrayCabeceras = [
            'font' => [
                'bold' => true,
                'size' => '11',
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ];

        $range = $start.$row.':'.$end.$row;
        $this->sheet
            ->getStyle($range)
            ->applyFromArray($styleArrayCabeceras);
        $this->sheet
            ->getStyle($range)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('215967');

        //        $this->sheet->setAutoFilter($range);

        $columnStart = \ord($start);
        $columnEnd = \ord($end);
        for ($i = $columnStart; $i <= $columnEnd; ++$i) {
            $this->sheet->getColumnDimension(\chr($i))->setAutoSize(true);
        }
    }

    private function sheetTitle(): string
    {
        return $this->sheet->getTitle();
    }
}

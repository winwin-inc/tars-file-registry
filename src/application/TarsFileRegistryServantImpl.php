<?php


namespace winwin\tars\file\application;


use kuiper\db\Criteria;
use kuiper\db\criteria\Sort;
use kuiper\db\TransactionManager;
use kuiper\di\annotation\Service;
use kuiper\helper\Arrays;
use winwin\tars\file\domain\TarsFile;
use winwin\tars\file\domain\TarsFileContent;
use winwin\tars\file\domain\TarsFileContentRepository;
use winwin\tars\file\domain\TarsFileRepository;
use winwin\tars\file\domain\TarsFileVersion;
use winwin\tars\file\domain\TarsFileVersionRepository;
use winwin\tars\file\servant\TarsFileRegistryServant;

/**
 * @Service()
 */
class TarsFileRegistryServantImpl implements TarsFileRegistryServant
{
    /**
     * @var TarsFileRepository
     */
    private $tarsFileRepository;

    /**
     * @var TarsFileVersionRepository
     */
    private $tarsFileVersionRepository;

    /**
     * @var TarsFileContentRepository
     */
    private $tarsFileContentRepository;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * TarsFileRegistryServantImpl constructor.
     * @param TarsFileRepository $tarsFileRepository
     * @param TarsFileVersionRepository $tarsFileVersionRepository
     * @param TarsFileContentRepository $tarsFileContentRepository
     * @param TransactionManager $transactionManager
     */
    public function __construct(
        TarsFileRepository $tarsFileRepository,
        TarsFileVersionRepository $tarsFileVersionRepository,
        TarsFileContentRepository $tarsFileContentRepository,
        TransactionManager $transactionManager)
    {
        $this->tarsFileRepository = $tarsFileRepository;
        $this->tarsFileVersionRepository = $tarsFileVersionRepository;
        $this->tarsFileContentRepository = $tarsFileContentRepository;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @inheritDoc
     */
    public function add($file)
    {
        $exist = $this->tarsFileRepository->findFirstBy(Criteria::create([
            'packageName' => $file->packageName,
            'revision' => $file->revision,
            'fileName' => $file->fileName
        ]));
        if ($exist) {
            $tarsFile = $exist;
        } else {
            $tarsFile = new TarsFile();
            $tarsFile->setPackageName($file->packageName)
                ->setRevision($file->revision)
                ->setFileName($file->fileName)
                ->setVersion(0);
        }
        $checksum = $this->generateChecksum($file->content);
        if ($tarsFile->getChecksum() === $checksum) {
            return $tarsFile->getVersion();
        }
        $tarsFile->setChecksum($checksum);
        return $this->transactionManager->transaction(function () use ($tarsFile, $file) {
            $tarsFile->setVersion($tarsFile->getVersion() + 1);
            $tarsFile->setSize(strlen($file->content));
            if ($tarsFile->getVersion() === 1) {
                $tarsFile->setFilePath("not_available");
                $this->tarsFileRepository->insert($tarsFile);
            }

            $tarsFile->setFilePath($this->buildFilePath($tarsFile->getId(), $tarsFile->getVersion()));
            $this->tarsFileRepository->update($tarsFile);
            $fileVersion = new TarsFileVersion();
            $fileVersion->setFileId($tarsFile->getId());
            $fileVersion->setVersion($tarsFile->getVersion());
            $fileVersion->setFilePath($tarsFile->getFilePath());
            $this->tarsFileVersionRepository->insert($fileVersion);
            $fileContent = new TarsFileContent();
            $fileContent->setFilePath($tarsFile->getFilePath());
            $fileContent->setContent($file->content);
            $this->tarsFileContentRepository->insert($fileContent);

            return $tarsFile->getVersion();
        });
    }

    /**
     * @inheritDoc
     */
    public function listFiles($packageName, $revision)
    {
        $files = $this->tarsFileRepository->findAllBy(Criteria::create([
            'packageName' => $packageName,
            'revision' => $revision,
        ]));
        return array_map(static function (TarsFile $file) {
            $fileDto = new \winwin\tars\file\servant\TarsFile();
            $fileDto->packageName = $file->getPackageName();
            $fileDto->fileName = $file->getFileName();
            $fileDto->revision = $file->getRevision();
            $fileDto->md5 = $file->getChecksum();
            return $fileDto;
        }, $files);
    }

    /**
     * @inheritDoc
     */
    public function listVersions($packageName, $revision, $fileName, $limit)
    {
        $exist = $this->tarsFileRepository->findFirstBy(Criteria::create([
            'packageName' => $packageName,
            'revision' => $revision,
            'fileName' => $fileName
        ]));
        if (!$exist) {
            return [];
        }
        $criteria = Criteria::create([
            'fileId' => $exist->getId()
        ])
            ->orderBy([Sort::of('version', Sort::DESC)]);
        if ($limit > 0) {
            $criteria->limit($limit);
        }
        $versions = $this->tarsFileVersionRepository->findAllBy($criteria);
        return Arrays::pull($versions, 'version');
    }

    /**
     * @inheritDoc
     */
    public function listRevisions($packageName)
    {
        $files = $this->tarsFileRepository->findAllBy(Criteria::create([
            'packageName' => $packageName,
        ]));
        return array_values(array_unique(Arrays::pull($files, 'revision')));
    }

    /**
     * @inheritDoc
     */
    public function getContent($query)
    {
        $exist = $this->tarsFileRepository->findFirstBy(Criteria::create([
            'packageName' => $query->packageName,
            'revision' => $query->revision,
            'fileName' => $query->fileName
        ]));
        if (!$exist) {
            return null;
        }
        if ($query->version > 0) {
            $criteria = Criteria::create([
                'fileId' => $exist->getId(),
                'version' => $query->version
            ]);
            $fileVersion = $this->tarsFileVersionRepository->findFirstBy($criteria);
            if (!$fileVersion) {
                return null;
            }
            $path = $fileVersion->getFilePath();
        } else {
            $path = $exist->getFilePath();
        }
        return $this->tarsFileContentRepository->findById($path)->getContent();
    }

    private function generateChecksum(string $content): string
    {
        return md5($content);
    }

    private function buildFilePath(int $fileId, int $version): string
    {
        return $fileId . '/' . $version;
    }
}
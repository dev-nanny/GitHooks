<?php

namespace DevNanny\Git;

class CommitDiff
{
    //Possible status letters are:
    const FILE_STATUS_ADDED         = 'A';
    const FILE_STATUS_COPIED        = 'C';
    const FILE_STATUS_DELETED       = 'D';
    const FILE_STATUS_MODIFIED      = 'M';
    const FILE_STATUS_RENAMED       = 'R';
    const FILE_STATUS_TYPE_CHANGED  = 'T';
    const FILE_STATUS_UNMERGED      = 'U';  // you must complete the merge before it can be committed)
    const FILE_STATUS_UNKNOWN       = 'X';  // most probably a bug, please report it to GIT

    /** @var RepositoryContainerInterface */
    private $repository;

    /**
     * @return RepositoryContainerInterface
     */
    final public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param RepositoryContainerInterface $repository
     */
    final public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    final public function __construct(RepositoryContainerInterface $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * @return array
     */
    final public function getFileList()
    {
        $repositoryContainer = $this->getRepository();
        $rawOutput = $repositoryContainer->getCommittedFiles();

        return $this->buildFileList($rawOutput);
    }

    /**
     * Split the raw output from git diff --cached -z into a list of files
     *
     * @param $rawOutput
     *
     * @return array
     */
    private function buildFileList($rawOutput)
    {
        /* Please note that the input is a single string, alternating file-paths
         * and file-status delimited by, and closed of with, a NULL character.
         * Hence the last entry will always be an empty value.
         */
        $files = array();

        $parts = explode("\x00", $rawOutput);

        $currentValueIsFilePath = false;
        foreach ($parts as $pathOrType) {
            if ($currentValueIsFilePath === true) {
                $files[] = $pathOrType;
            }
            $currentValueIsFilePath = !$currentValueIsFilePath;
        };

        return $files;
    }
}

/*EOF*/
import { v4 as uuid } from "uuid";

const UUIDs = new Map<string, true>();

export const generateUUID = () => {
  let UUID = uuid();

  while (UUIDs.get(UUID)) UUID = uuid();

  UUIDs.set(UUID, true);

  return UUID;
};
